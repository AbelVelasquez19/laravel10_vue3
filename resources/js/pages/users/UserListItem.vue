<script setup> 
    import { ref } from 'vue';
    import {useToastr} from '../../toastr.js'
    import {formDate} from '../../helper.js';
    const toastr = useToastr();
    const props = defineProps({
        user:Object,
        index:Number,
        selectAll:Boolean,
    })

    const emit = defineEmits(['userDeleted','editUser','confirmUserDeletion']);
    

    const editUser = (user) =>{
        emit('editUser', user)
    }

    const confirmUserDeletion = (id) =>{
        emit('confirmUserDeletion', id)
    }

    const roles = ref([
       {
            name:'ADMIN',
            value:1
       } ,
       {
            name:'USER',
            value:2
       } ,
    ]);
    const changeRole = async(user, role) =>{
        await axios.patch(`/api/users/${user.id}/change-role`,{
            role:role,
        })
        .then((response) => {
            toastr.success('Role change successfully!');
        })
    }

    const toggleSelection = () => {
        emit('toggleSelection', props.user)
    }
</script>
<template>
     <tr >
        <td><input type="checkbox" :checked="selectAll" @change="toggleSelection"></td>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td class="text-center">{{ formDate(user.created_at) }}</td>
        <td class="text-center">
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :key="role.value" :value="role.value" :selected="(user.role === role.name)">{{ role.name }}</option>
            </select>
        </td>
        <td class="text-center">
            <a href="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="$event=>$emit('confirmUserDeletion',user.id)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>

   
</template>