<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function addTask(Request $request){
        $task = new Task($request->all());
        $task->project_id = $request->parent_id;
        $task->save();
        return response()->json($task, 201);
    }
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    public function show(Project $project, Task $task)
    {
        $task->load('comments');
        return response()->json($task);
    }

    public function store(Request $request, Project $project)
    {
        $task = new Task($request->all());
        $task->project_id = $project->id;
        $task->save();
        return response()->json($task, 201);
    }

    public function update(Request $request)
    {
        $task = Task::find($request->parent_id);
        $task->update($request->all());
        return response()->json($task);
    }

public function delete(){
    Task::find(1)->delete();
    return response()->json('Task deleted');
}
    public function assignTask(Request $request){
        $task = Task::find(1);
        $task->assigned_user_id = $request->user_id;
        $task->save();
        return response()->json(null, 204);
    }
    public function destroy(Project $project, Task $task)
    {
        $task=Task::where('id',1);
        $task->delete();
        return response()->json(null, 204);
    }

    public function assign(Request $request, Project $project, Task $task)
    {
        $task->assigned_user_id = $request->user_id;
        $task->save();
        return response()->json($task);
    }
}

