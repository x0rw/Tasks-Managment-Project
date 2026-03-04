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
    public function index(Project $project)
    {
        // Redirect to project view - all tasks are shown there
        return redirect()->route('projects.show', $project);
    }

    public function create(Project $project)
    {
        $users = User::all();
        $tags  = Tag::all();
        return view('tasks.create', compact('users', 'project', 'tags'));
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task = $project->tasks()->create($data);

        // Sync tags
        $task->tags()->sync($request->tags ?? []);

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task created successfully.');
    }

    public function edit(Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $users = User::all();
        $tags  = Tag::all();
        return view('tasks.edit', compact('task', 'users', 'tags', 'project'));
    }

    public function show(Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $task->load(['comments.user', 'tags', 'assignedUser']);
        return view('tasks.show', compact('task', 'project'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

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

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task updated successfully.');
    }

    public function updateAssignment(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $task->update([
            'assigned_user_id' => $request->assigned_user_id,
        ]);

        return back()->with('success', 'Assignment updated.');
    }

    public function updateStatus(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

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

    public function destroy(Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $task->delete();
        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task deleted successfully.');
    }

    public function updatePriority(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $user = auth()->user();

        // Only admin/manager may update priority
        if (! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Only admin or manager can update task priority.');
        }

        $request->validate([
            'priority' => 'required|in:low,medium,high',
        ]);

        $task->update([
            'priority' => $request->priority,
        ]);

        return back()->with('success', 'Priority updated.');
    }

    public function updateDueDate(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $user = auth()->user();

        // Only admin/manager may update due date
        if (! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'Only admin or manager can update task due date.');
        }

        $request->validate([
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Due date updated.');
    }

    public function updateTags(Request $request, Project $project, Task $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $request->validate([
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->tags()->sync($request->tags ?? []);

        return back()->with('success', 'Tags updated.');
    }
}
