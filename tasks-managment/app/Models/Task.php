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

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class)->latest();
    }
}
