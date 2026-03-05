<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id',
        'uploaded_by',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
