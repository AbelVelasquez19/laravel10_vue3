<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Enums\AppintmentStatus;
class AppointmentController extends Controller
{
    public function index(){
        return Appointment::query()
            ->with('client:id,firts_name,last_name')
            ->when(request('status'), function($query){
                return $query->where('status', AppintmentStatus::from(request('status')));
            })
            ->latest()
            ->paginate(15)
            ->through(fn($appoinment) => [
                'id'=>$appoinment->id,
                'start_time'=>$appoinment->start_time->format('Y-m-d h-i A'),
                'end_time'=>$appoinment->end_time->format('Y-m-d h-i A'),
                'status'=>[
                    'name'=>$appoinment->status->name,
                    'color'=>$appoinment->status->color(),
                ],
                'client'=>$appoinment->client,
            ]);
    }

    public function store(){
        Appointment::create([
            'title'=>request('title'),
            'client_id'=>1,
            'start_time'=>now(),
            'end_time'=>now(),
            'description'=>request('description'),
            'status'=>AppintmentStatus::SCHEDULED,
        ]);
    }
}
