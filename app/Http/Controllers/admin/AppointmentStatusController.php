<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\AppintmentStatus;
use App\Models\Appointment;

class AppointmentStatusController extends Controller
{
    public function getStatusWithCount(){
        $cases = AppintmentStatus::cases();
       
        return collect($cases)->map(function($status){
            return[
                'name'=>$status->name,
                'value'=>$status->value,
                'count'=>Appointment::where('status',$status->value)->count(),
                'color'=>AppintmentStatus::from($status->value)->color(),
            ];
        });
    }
}
