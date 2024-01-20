<script setup>
import axios from 'axios';
import { ref, onMounted, reactive,watch } from 'vue';
import {Form, Field } from 'vee-validate';
import * as yup from 'yup';

const users = ref([]);
const editing = ref(false);
const formValues = ref();
const form = ref(null);

const  getUsers = async() => {
    let result = await axios.get('/api/users');
    if(result.data.length > 0){
        users.value = result.data;
        console.log(result.data)
    }
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

const createUser = async(values, {resetForm}) => {
    await axios.post('/api/users', values)
    .then((response) => {
        users.value.unshift(response.data)
        $('#createUserModal').modal('hide');
        resetForm();
    })
}

const addUser = () => {
    editing.value = false;
    $('#createUserModal').modal('show');
}

const editUser = (user) => {
    editing.value = true;
    console.log(user.id)
    form.value.resetForm();
    $('#createUserModal').modal('show');
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
};

const updateUser = async (values) => {
    await axios.put('/api/users/'+values.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
            $('#createUserModal').modal('hide');
        }).catch((error) => {
            console.log(error)
        }).finally(()=>{
            form.value.resetForm();
        })
}

const handlerSubmit = (values, actions) => {
    console.log(actions)
    if(editing.value){
        updateUser(values, actions);
    }else{
        createUser(values, actions);
    }
}

watch(formValues, (newValues) => {
    if (form.value) {
        form.value.setValues(newValues);
    }
});

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
            <button @click="addUser" type="button" class="mb-2 btn btn-primary">Nuevo</button>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Fecha</th>
                                <th>Rol</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-center">
                                    <a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
</template>