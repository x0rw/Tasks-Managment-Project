@extends('layouts.app')

@section('content')

<div class="max-w-xl mx-auto">

    {{-- Page header --}}
    <div class="mb-6">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1 text-sm text-base-content/50 hover:text-base-content transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Tasks
        </a>
        <h1 class="text-2xl font-bold tracking-tight">Create Task</h1>
        <p class="text-base-content/60 text-sm mt-1">Add a new task to your workspace</p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div role="alert" class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form card --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <form action="{{ route('projects.tasks.store', ["project_id" => $project->id]) }}" method="POST" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div class="form-control">
                    <label class="label" for="title">
                        <span class="label-text font-medium">Title</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="e.g. Design landing page"
                           class="input input-bordered w-full {{ $errors->has('title') ? 'input-error' : '' }}">
                </div>

                {{-- Description --}}
                <div class="form-control">
                    <label class="label" for="description">
                        <span class="label-text font-medium">Description <span class="text-base-content/40 font-normal">(optional)</span></span>
                    </label>
                    <textarea name="description" id="description" rows="3"
                              placeholder="Describe the task..."
                              class="textarea textarea-bordered w-full resize-none">{{ old('description') }}</textarea>
                </div>

                {{-- Status & Priority --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label" for="status">
                            <span class="label-text font-medium">Status</span>
                        </label>
                        <select name="status" id="status" required class="select select-bordered w-full">
                            @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status') == $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label" for="priority">
                            <span class="label-text font-medium">Priority</span>
                        </label>
                        <select name="priority" id="priority" required class="select select-bordered w-full">
                            @foreach(['low', 'medium', 'high'] as $priority)
                                <option value="{{ $priority }}" @selected(old('priority') == $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Due Date --}}
                <div class="form-control">
                    <label class="label" for="due_date">
                        <span class="label-text font-medium">Due Date <span class="text-base-content/40 font-normal">(optional)</span></span>
                    </label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                           class="input input-bordered w-full">
                </div>


                {{-- Assign User --}}
                <div class="form-control">
                    <label class="label" for="assigned_user_id">
                        <span class="label-text font-medium">Assign to User <span class="text-base-content/40 font-normal">(optional)</span></span>
                    </label>
                    <select name="assigned_user_id" id="assigned_user_id" class="select select-bordered w-full">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('assigned_user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-primary flex-1">Create Task</button>
                    <a href="{{ route('projects.index', ["project_id" => request()->route()->parameter('project_id')]) }}" class="btn btn-ghost flex-1">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>

@endsection
