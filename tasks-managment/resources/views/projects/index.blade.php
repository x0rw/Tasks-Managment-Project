@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Projects</h1>
        <p class="text-base-content/60 text-sm mt-1">Manage your workspace projects</p>
    </div>
    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Project
    </a>
    @endif
</div>


@if($projects->isEmpty())
    <div class="card bg-base-100 shadow">
        <div class="card-body items-center text-center py-16">
            <svg class="w-10 h-10 opacity-30 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            <p class="text-base-content/50 text-sm">No projects yet.</p>
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm mt-3">Create your first one</a>
            @endif
        </div>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($projects as $project)
        <div class="card bg-base-100 shadow hover:shadow-md transition-shadow duration-200">
            <div class="card-body gap-3">
                <div class="flex-1">
                    <h3 class="card-title text-base truncate">{{ $project->name }}</h3>
                    <p class="text-base-content/60 text-sm line-clamp-2 leading-relaxed mt-1">
                        {{ $project->description ?: 'No description.' }}
                    </p>
                </div>

                <div class="flex items-center gap-2 pt-1 border-t border-base-200">
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-6 h-6 text-xs font-bold">
                            <span>{{ strtoupper(substr($project->owner->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <span class="text-xs text-base-content/50">{{ $project->owner->name }}</span>
                </div>
                    <span class="text-xs text-base-content/100">number of tasks: {{ $project->tasks->count()}}</span>

                <div class="card-actions justify-end gap-1">
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-xs">View</a>
                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-ghost btn-xs">Edit</a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('Delete this project?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
