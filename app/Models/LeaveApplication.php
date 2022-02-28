<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id', 'id');
    }
}
