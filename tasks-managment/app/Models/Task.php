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
        'project_id',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
}
