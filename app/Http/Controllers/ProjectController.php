<?PHP
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::create($request->all());

        return response()->json($project, 201);
    }


    public function index2()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    public function show2(Project $project)
    {
        $project->load('users', 'tasks');
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update($request->all());

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(null, 204);
    }

    public function assign(Request $request, Project $project)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $project->assigned_user_id= $user->id;
        $project->save();

        return response()->json(['message' => 'User assigned to project successfully']);
    }
}
