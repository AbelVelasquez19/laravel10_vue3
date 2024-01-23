<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AppintmentStatus;
class Appointment extends Model
{
    use HasFactory;
    protected $guarded =[];
    protected $casts = [
        'start_time'=>'datetime',
        'end_time'=>'datetime',
        'status'=>AppintmentStatus::class,
    ];

    public function client() //BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
}
