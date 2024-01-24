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

        $validated = request()->validate([
            'client_id'=>'required',
            'title'=>'required',
            'description'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
        ],[
            'client_id.required' => 'Nombre del cliente es requerido',
        ]);

        Appointment::create([
            'title'=>$validated['title'],
            'client_id'=>$validated['client_id'],
            'start_time'=>$validated['start_time'],
            'end_time'=>$validated['end_time'],
            'description'=>$validated['description'],
            'status'=>AppintmentStatus::SCHEDULED,
        ]);

        return response()->json(['message'=>'success']);
    }
}
