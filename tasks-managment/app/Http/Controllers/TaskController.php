<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index($projectID)
    {
        $tasks = Task::where(['project_id' => $projectID])->get();
        $users = User::all();
        $project = Project::findOrFail($projectID);

        return view('tasks.index', compact('tasks', 'users', 'project'));
    }

    public function create($projectID)
    {
        $users = User::all();
        $tags  = Tag::all();
        $project = Project::findOrFail($projectID);
        return view('tasks.create', compact('users', 'project', 'tags'));
    }

    public function store(Request $request, string $projectID)
    {

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);


        $data['project_id'] = $projectID;
        $project = Project::findOrFail($projectID);
        $task = $project->tasks()->create($data);

        // Sync tags
        $task->tags()->sync($request->tags ?? []);

        return redirect()->route('projects.show', ["project" => $project])->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $users = User::all();
        $tags  = Tag::all();
        return view('tasks.edit', compact('task', 'users', 'tags'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task->update($data);

        // Sync tags
        $task->tags()->sync($request->tags ?? []);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function updateAssignment(Request $request, Task $task)
    {
        $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task->update([
            'assigned_user_id' => $request->assigned_user_id,
        ]);

        return back()->with('success', 'Assignment updated.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $user = auth()->user();

        // Only the assigned user OR an admin/manager may update status
        $isAssigned   = $task->assigned_user_id && $task->assigned_user_id === $user->id;
        $isPrivileged = $user->hasAnyRole(['admin', 'manager']);

        if (! $isAssigned && ! $isPrivileged) {
            abort(403, 'Only the assigned user or an admin/manager can update task status.');
        }

        $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update([
            'status' => $request->status,
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
