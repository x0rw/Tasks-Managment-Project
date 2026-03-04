@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Tasks</h1>
        <p class="text-base-content/60 text-sm mt-1">All tasks across your workspace</p>
    </div>
    <a href="{{ route('projects.tasks.create', $project ) }}" class="btn btn-primary btn-sm gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Task
    </a>
</div>

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
                    <th>Tags</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>

                    {{-- Title --}}
                    <td class="font-medium">{{ $task->title }}</td>

                    {{-- Description --}}
                    <td class="text-base-content/60 max-w-xs">
                        <span class="line-clamp-1">{{ $task->description ?: '—' }}</span>
                    </td>

                    {{-- Status select --}}
                    <td>
                        <form action="{{ route('projects.tasks.updateStatus', [$project, $task]) }}" method="POST">
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

                    {{-- Priority select (admin/manager only) --}}
                    <td>
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                            <form action="{{ route('projects.tasks.updatePriority', [$project, $task]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="priority" onchange="this.form.submit()"
                                        class="select select-xs w-auto
                                        {{ $task->priority === 'high' ? 'select-error' : '' }}
                                        {{ $task->priority === 'medium' ? 'select-warning' : '' }}
                                        {{ $task->priority === 'low' ? 'select-info' : '' }}">
                                    <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </form>
                        @else
                            @php
                                $badge = match($task->priority) {
                                    'high'   => 'badge-error',
                                    'medium' => 'badge-warning',
                                    default  => 'badge-info',
                                };
                            @endphp
                            <span class="badge badge-sm {{ $badge }}">{{ ucfirst($task->priority) }}</span>
                        @endif
                    </td>

                    {{-- Due date (admin/manager can edit) --}}
                    <td>
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                            <form action="{{ route('projects.tasks.updateDueDate', [$project, $task]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="date" name="due_date" 
                                       value="{{ $task->due_date }}"
                                       onchange="this.form.submit()"
                                       class="input input-xs input-bordered w-auto">
                            </form>
                        @else
                            <span class="text-base-content/60">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M j, Y') : '—' }}</span>
                        @endif
                    </td>

                    {{-- Tags --}}
                    <td>
                        <div x-data="{
                            open: false,
                            top: 0,
                            left: 0,
                            toggleOpen(event) {
                                this.open = !this.open;
                                if (this.open && event) {
                                    const rect = event.currentTarget.getBoundingClientRect();
                                    this.top = rect.bottom + window.scrollY + 8;
                                    this.left = rect.left + window.scrollX;
                                }
                            }
                        }" class="relative">
                            {{-- Display tags with truncation --}}
                            <button @click="toggleOpen($event)" class="flex flex-wrap gap-0.5 items-center max-w-xs text-left">
                                @forelse($task->tags->take(2) as $tag)
                                    <span class="badge badge-xs" style="background-color: {{ $tag->color }}; color: #fff; border: none; font-size: 10px;">
                                        {{ Str::limit($tag->name, 8) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-base-content/40">+ Add</span>
                                @endforelse
                                @if($task->tags->count() > 2)
                                    <span class="text-xs text-base-content/40">+{{ $task->tags->count() - 2 }}</span>
                                @endif
                            </button>
                            
                            {{-- Tags dropdown modal with fixed positioning --}}
                            <template x-if="open">
                                <div @click="open = false" 
                                     class="fixed inset-0 z-40" style="background: rgba(0,0,0,0.1);">
                                </div>
                            </template>
                            <div x-show="open" x-cloak
                                 class="fixed z-50 p-3 bg-base-100 border border-base-300 rounded-lg shadow-2xl min-w-[220px] max-w-sm"
                                 @click.stop
                                 :style="`top: ${top}px; left: ${left}px;`"
                                 style="display: none;">
                                <form action="{{ route('projects.tasks.updateTags', [$project, $task]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-1.5 max-h-56 overflow-y-auto">
                                        @forelse($tags as $tag)
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-base-200 p-1.5 rounded">
                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                                       class="checkbox checkbox-xs"
                                                       {{ $task->tags->contains($tag->id) ? 'checked' : '' }}>
                                                <span class="badge badge-xs" style="background-color: {{ $tag->color }}; color: #fff; border: none;">
                                                    {{ $tag->name }}
                                                </span>
                                            </label>
                                        @empty
                                            <p class="text-xs text-base-content/50 p-2">No tags available</p>
                                        @endforelse
                                    </div>
                                    <div class="flex gap-2 mt-3 pt-2 border-t border-base-300">
                                        <button type="submit" class="btn btn-primary btn-xs flex-1">Save</button>
                                        <button type="button" @click="open = false" class="btn btn-ghost btn-xs">Done</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>

                    {{-- Assign select --}}
                    <td>
                        <form action="{{ route('projects.tasks.updateAssignment', [$project, $task]) }}" method="POST">
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
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="btn btn-ghost btn-xs">Edit</a>
                            <form action="{{ route('projects.tasks.destroy', [$project, $task]) }}" method="POST"
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

@endsection
