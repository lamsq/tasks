<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        //Authentication check
        if (!Auth::check()) 
            return response() -> json(['message' => 'Unauthorized'], 401);
        
        //Filter and sort of the tasks
        $tasks = Task::where('user_id', Auth::id())
            ->when($request->status, fn($query) => $query ->where('status', $request->status))
            ->when($request ->due_date, fn($query) => $query-> whereDate('due_date', $request->due_date))
            ->orderBy('due_date')
            ->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        //Authentication check
        if (!Auth::check()) 
            return response()-> json(['message'=>'Unauthorized'], 401);
        
        $validated = $request -> validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:new,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create(array_merge($validated, ['user_id' => Auth::id()]));
        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);

        //Access check
        if (Gate::denies('view', $task)) 
            return response()->json(['message' => 'Forbidden'], 403);
        
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        //Access check
        if (Gate::denies('update', $task)) 
            return response()->json(['message' => 'Forbidden'], 403);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:new,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if (Gate::denies('delete', $task)) 
            return response()->json(['message' => 'Forbidden'], 403);
        
        $task->delete();
        return response()->json(null, 204);
    }
}