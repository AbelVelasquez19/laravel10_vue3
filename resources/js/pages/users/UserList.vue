<script setup>
import axios from 'axios';
import { ref, onMounted, reactive,watch } from 'vue';
import {Form, Field, useSetFormErrors } from 'vee-validate';
import * as yup from 'yup';
import {useToastr} from '../../toastr.js';
import UserListItem from './UserListItem.vue';
import debounce from 'lodash.debounce'
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const users = ref({'data':[]});
const editing = ref(false);
const formValues = ref();
const form = ref(null);
const searchQuery = ref(null);
const selectedUsers = ref([]);
const selectAll = ref(false);

const emit = defineEmits(['userDeleted']);

const  getUsers = async(page = 1) => {
    await axios.get(`/api/users?page=${page}`,{
        params:{
            query:searchQuery.value
        }
    })
    .then((response) => {
        users.value = response.data;
        selectedUsers.value=[];
        selectAll.value = false;
    })
}

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
})

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) =>{
        return password ? schema.required().min(8) : schema;
    }),
})

const createUser = async(values, {resetForm,setErrors}) => {
    await axios.post('/api/users', values)
    .then((response) => {
        users.value.data.unshift(response.data)
        $('#createUserModal').modal('hide');
        resetForm();
        toastr.success('User created successfully!');
    })
    .catch((error)=>{
        if(error.response.data.errors){
            setErrors(error.response.data.errors);
        }
        //setFieldError('email',error.response.data.errors.email[0])
    })
}

const addUser = () => {
    editing.value = false;
    $('#createUserModal').modal('show');
}

const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    $('#createUserModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = async (values,{setErrors}) => {
    await axios.put('/api/users/'+values.id, values)
        .then((response) => {
            console.log(response)
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
            $('#createUserModal').modal('hide');
            toastr.success('User update successfully!');
        }).catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        })
}

const handlerSubmit = (values, actions) => {
    if(editing.value){
        updateUser(values, actions);
    }else{
        createUser(values, actions);
    }
}



const toggleSelection = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if(index === -1){
        selectedUsers.value.push(user.id)
    }else{
        selectedUsers.value.splice(index, 1)
    }
}

const bulkDelete = async() => {
    await axios.delete('/api/users',{
        data:{
            ids:selectedUsers.value
        }
    })
    .then((response) => {
        users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
        selectedUsers.value=[];
        selectAll.value = false;
        toastr.success(response.data.message);
    })
}


const selectAllUsers = () => {
    if(selectAll.value){
        selectedUsers.value = users.value.data.map(user => user.id);
        console.log(users.value.data)
    }else{
        selectedUsers.value = [];
    }
    console.log(selectedUsers.value)
}

const userIdBeingDeleted = ref(null);

const confirmUserDeletion = (id) => {
    $('#deleteUserModal').modal('show');
    userIdBeingDeleted.value = id;
}

const deleteUser = async() => {
    await axios.delete(`/api/users/${userIdBeingDeleted.value}`)
    .then((response) => {
        $('#deleteUserModal').modal('hide');
        //users.value = users.value.filter(user=>user.id !== userIdBeingDeleted.value);
        users.value.data = users.value.data.filter(user=>user.id !== userIdBeingDeleted.value);
        toastr.success('User delete successfully!');
    })
}

watch(formValues, (newValues) => {
    if (form.value) {
        form.value.setValues(newValues);
    }
});

watch(searchQuery, debounce(()=>{
    getUsers();
},300))

onMounted(() => {
    getUsers();
});

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <button @click="addUser" type="button" class="mb-2 btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Nuevo</button>
                    <div v-if="selectedUsers.length > 0">
                        <button  @click="bulkDelete" type="button" class="mb-2 btn btn-danger ml-2"> <i class="fa fa-trash mr-1"></i> Eliminar selecionado</button>
                        <span  class="badge badge-info right ml-2">Selected {{ selectedUsers.length }} users</span>
                    </div>
                </div>
                <div>
                    <input type="text" class="form-control" placeholder="Search..." v-model="searchQuery">
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers"/></th>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Fecha</th>
                                <th>Rol</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                           <UserListItem v-for="(user, index) in users.data"
                                     :key="user.id"
                                     :user=user
                                     :index=index
                                     @confirm-user-deletion="confirmUserDeletion"
                                     @edit-user="editUser"
                                     @toggle-selection="toggleSelection"
                                     :select-all="selectAll"
                            />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No result found...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers"/>
        </div>
    </div>

    <div class="modal fade" id="createUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span v-if="editing">Editar usuario</span>
                        <span v-else>Agregar nuevo usuario</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <Form ref="form" @submit="handlerSubmit" :validation-schema="editing ? editUserSchema:createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <field type="text" class="form-control" id="name" :class="{'is-invalid': errors.name}" aria-describedby="" placeholder="Nombre completo" name="name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="name">Email</label>
                            <field type="email" class="form-control" id="email" :class="{'is-invalid': errors.email}" aria-describedby="" placeholder="Email"  name="email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="name">Password</label>
                            <field type="password" class="form-control" id="password" :class="{'is-invalid': errors.password}" aria-describedby="" placeholder="Password" name="password" />
                            <span class="invalid-feedback">{{ errors.password }}</span> 
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deleteUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">
                        <span>Eliminar usuario</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <span>Are you sure you want to delete this user ? </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</template>