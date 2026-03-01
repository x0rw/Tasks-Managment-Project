<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_user_id',
        'user_id',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedProject()
    {
        return $this->belongsTo(User::class, 'project_id');
    }
}
