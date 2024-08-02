<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Project $project, Task $task)
    {
        $comments = $task->comments()->with('replies')->get();
        return response()->json($comments);
    }

    public function store(Request $request, Project $project, Task $task)
    {
        $comment = new Comment($request->all());
        $comment->task_id = $task->id;
        $comment->save();
        return response()->json($comment, 201);
    }

    public function update(Request $request, Project $project, Task $task, Comment $comment)
    {
        $comment->update($request->all());
        return response()->json($comment);
    }

    public function destroy(Project $project, Task $task, Comment $comment)
    {
        $comment->delete();
        return response()->json(null, 204);
    }
}

