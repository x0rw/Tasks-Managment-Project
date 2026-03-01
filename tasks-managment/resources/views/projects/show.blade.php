@extends('layouts.app')

@section('content')

<div class="max-w-x mx-auto">

    <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Projects
    </a>

    <div class="card bg-base-100 shadow">
        <div class="card-body">

            {{-- Project name + owner --}}
            <h1 class="card-title text-xl">{{ $project->name }}</h1>

            <div class="flex items-center gap-2 text-sm text-base-content/50">
                <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content rounded-full w-6 h-6 text-xs font-bold">
                        <span>{{ strtoupper(substr($project->owner->name, 0, 1)) }}</span>
                    </div>
                </div>
                {{ $project->owner->name }}
            </div>

            <divider class="divider my-2"></divider>

            @if($project->description)
                <p class="text-base-content/70 text-sm leading-relaxed">{{ $project->description }}</p>
            @else
                <p class="text-base-content/40 text-sm italic">No description provided.</p>
            @endif


    <a href="{{ route('projects.tasks.create', $project ) }}" class="btn btn-primary btn-sm gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
    </a>
{{-- Table card --}}
<div class="card bg-base-100 shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Created By</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->tasks as $task)
                <tr>

                    {{-- Title --}}
                    <td class="font-medium">{{ $task->title }}</td>

                    {{-- Description --}}
                    <td class="text-base-content/60 max-w-xs">
                        <span class="line-clamp-1">{{ $task->description ?: '—' }}</span>
                    </td>

                    {{-- Status select --}}
                    <td>
                        <form action="{{ route('tasks.updateStatus', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                    class="select select-xs w-auto
                                    {{ $task->status === 'done'        ? 'select-success' : '' }}
                                    {{ $task->status === 'in_progress' ? 'select-warning' : '' }}
                                    {{ $task->status === 'todo'        ? 'select-ghost'   : '' }}">
                                <option value="todo"        {{ $task->status === 'todo'        ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="done"        {{ $task->status === 'done'        ? 'selected' : '' }}>Done</option>
                            </select>
                        </form>
                    </td>

                    {{-- Priority badge --}}
                    <td>
                        @php
                            $badge = match($task->priority) {
                                'high'   => 'badge-error',
                                'medium' => 'badge-warning',
                                default  => 'badge-info',
                            };
                        @endphp
                        <span class="badge badge-sm {{ $badge }} badge-soft">{{ ucfirst($task->priority) }}</span>
                    </td>

                    {{-- Due date --}}
                    <td class="text-base-content/60">
                        {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M j, Y') : '—' }}
                    </td>

                    {{-- Creator --}}
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-6 h-6 text-xs">
                                    <span>{{ strtoupper(substr($task->user->name ?? 'U', 0, 1)) }}</span>
                                </div>
                            </div>
                            <span class="text-xs">{{ $task->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>

                    {{-- Assign select --}}
                    <td>
                        <form action="{{ route('tasks.updateAssignment', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="assigned_user_id" onchange="this.form.submit()"
                                    class="select select-xs select-ghost w-auto">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $task->assigned_user_id === $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-ghost btn-xs">Edit</a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                  onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                            </form>
                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-base-content/50 text-sm">No tasks yet. Create your first one!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>




            <div class="card-actions justify-start gap-2 pt-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary btn-sm">Edit</a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Delete this project?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm text-error">Delete</button>
                </form>
            </div>

        </div>
    </div>

</div>

@endsection
