<script setup>
import axios from 'axios';
import { ref, onMounted, reactive } from 'vue';
import {Form, Field } from 'vee-validate';

const users = ref([]);
const form = reactive({
    name:'',
    email:'',
    password:''
})
const  getUsers = async() => {
    let result = await axios.get('/api/users');
    if(result.data.length > 0){
        users.value = result.data;
        console.log(result.data)
    }
}

const createUser = async() => {
    await axios.post('/api/users', form)
    .then((response) => {
        users.value.unshift(response.data)
        form.name = '';
        form.email = '';
        form.password = '';
        $('#createUserModal').modal('hide');
    })
}

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
            <button type="button" class="mb-2 btn btn-primary" data-toggle="modal" data-target="#createUserModal">Nuevo</button>
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
                                <td></td>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Agregar nuevo usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" aria-describedby="" placeholder="Nombre completo" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="name">Email</label>
                            <input type="email" class="form-control" id="email" aria-describedby="" placeholder="Email" v-model="form.email">
                        </div>
                        <div class="form-group">
                            <label for="name">Password</label>
                            <input type="password" class="form-control" id="password" aria-describedby="" placeholder="Password" v-model="form.password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="createUser">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>