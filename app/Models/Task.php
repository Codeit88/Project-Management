<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['name', 'project_id', 'assigned_user_id', 'parent_id'];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    // A task can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // A task can be assigned to a user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
