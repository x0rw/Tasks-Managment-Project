<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'title',
        'content',
        'task_id',
        'user_id',
    ];


public function tasks()
    {
        return $this->belongsTo(Task::class);
    }
public function user()

{
return  $this->hasMany(Users::class);
}


}
