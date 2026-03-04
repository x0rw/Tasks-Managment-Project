<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Display all projects
    public function index()
    {
        $projects = Project::with('owner')->latest()->get();
        $users = User::all();
        return view('projects.index', compact('projects', 'users'));
    }

    // Show create form
    public function create()
    {
        return view('projects.create');
    }

    // Store new project
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'owner_id'    => auth()->id(),
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    // Show single project
    public function show(Project $project)
    {
        $project->load('tasks.tags');
        $users = User::all();
        $tags = \App\Models\Tag::all();
        return view('projects.show', compact('project', 'users', 'tags'));
    }

    // Show edit form
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($request->only('name', 'description'));

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    // Delete project
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

