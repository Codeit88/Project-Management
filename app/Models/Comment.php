<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'task_id', 'parent_id'];

    // A comment belongs to a task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // A comment can have many replies (comments)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
