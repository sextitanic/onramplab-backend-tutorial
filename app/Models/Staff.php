<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\LeaveApplication;
use App\Models\StaffLeave;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $casts = [
        'is_probation' => 'boolean'
    ];

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function leaveBalance()
    {
        return $this->hasOne(StaffLeave::class, 'id', 'staff_id')->where('year', date('Y'));
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'id', 'staff_id');
    }
}
