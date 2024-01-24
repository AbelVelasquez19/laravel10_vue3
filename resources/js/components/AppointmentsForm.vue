<script setup>
import { reactive, onMounted, ref } from 'vue';
import { useRouter,useRoute } from 'vue-router';
import { useToastr } from '@/toastr';
import {Form, Field, useSetFormErrors } from 'vee-validate';
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/light.css';

const router = useRouter();
const route = useRoute();
const toastr = useToastr();
const form = reactive({
    title:'',
    client_id:'1',
    start_time:'',
    end_time:'',
    description:'',
});

const handleSubmit = async(values, actions)=>{
    axios.post('/api/appointments/create', form)
        .then((response) => {
          router.push('/admin/appointments');
          toastr.success('Cita creado correctamente!');
    })
    .catch((error)=>{
        actions.setErrors(error.response.data.errors)
    })
}

const clients = ref();
const getClients = () => {
    axios.get('/api/clients')
        .then((response) => {
            clients.value = response.data;
    })
}

const getAppointment = () => {
    axios.get(`/api/appointments/${route.params.id}/edit`)
        .then((response) => {
            form.title = response.data.title
    })
};

const editMode = ref(false);

onMounted(()=>{
    if(route.name ==='admin.appointments.edit'){
        editMode.value = true;
        getAppointment();
    }
    flatpickr(".flatpickr",{    
        enableTime: true,
        dateFormat:'Y-m-d h:i K',
        defaultHour:10
    })
    getClients();
})
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Editar</span>
                        <span v-else>Crear Cita</span>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Inicio</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointments">Citas</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Editar</span>
                            <span v-else>Crear</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" :class="{'is-invalid':errors.title}" placeholder="Enter Title" v-model="form.title">
                                            <span class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client">Client Name</label>
                                            <select id="client" class="form-control"  :class="{'is-invalid':errors.client_id}" v-model="form.client_id">
                                                <option v-for="client in clients" :key="client.id" :value="client.id">{{client.firts_name+' '+client.last_name}}</option>
                                            </select>
                                            <span class="invalid-feedback">{{ errors.client_id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Start Time</label>
                                            <input type="text" class="form-control flatpickr" :class="{'is-invalid':errors.start_time}" id="start_date" v-model="form.start_time">
                                            <span class="invalid-feedback">{{ errors.start_time }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_time">End Time</label>
                                            <input type="text" class="form-control flatpickr" :class="{'is-invalid':errors.end_time}" id="end_time" v-model="form.end_time">
                                            <span class="invalid-feedback">{{ errors.end_time }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" rows="3" :class="{'is-invalid':errors.description}"
                                        placeholder="Enter Description" v-model="form.description"></textarea>
                                    <span class="invalid-feedback">{{ errors.description }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
