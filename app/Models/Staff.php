<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;

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
}
