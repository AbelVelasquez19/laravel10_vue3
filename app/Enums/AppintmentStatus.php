<?php 

namespace App\Enums;

enum AppintmentStatus:int
{
    case SCHEDULED = 1;
    case CONFIRMED = 2;
    case CANCELLED = 3;

    public function color(): string 
    {
        return match($this){
            AppintmentStatus::SCHEDULED => 'primary',
            AppintmentStatus::CONFIRMED => 'success',
            AppintmentStatus::CANCELLED => 'danger',
        };
    }
}