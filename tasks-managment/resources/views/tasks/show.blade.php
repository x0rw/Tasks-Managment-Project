@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- Back link --}}
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-base-content/50 hover:text-base-content transition-colors mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>

    {{-- Task summary card --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body gap-2">
            <div class="flex items-start justify-between gap-4">
                <h1 class="card-title text-xl">{{ $task->title }}</h1>
                {{-- Status badge --}}
                @php
                    $statusClass = match($task->status) {
                        'done'        => 'badge-success',
                        'in_progress' => 'badge-warning',
                        default       => 'badge-ghost',
                    };
                    $statusLabel = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'][$task->status];
                @endphp
                <span class="badge badge-sm {{ $statusClass }} badge-soft shrink-0">{{ $statusLabel }}</span>
            </div>

            @if($task->description)
                <p class="text-base-content/70 text-sm leading-relaxed">{{ $task->description }}</p>
            @endif

            <div class="flex flex-wrap gap-3 text-xs text-base-content/50 mt-1">
                @if($task->assignedUser)
                    <span>👤 {{ $task->assignedUser->name }}</span>
                @endif
                @if($task->due_date)
                    <span>📅 {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}</span>
                @endif
                @if($task->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1">
                        @foreach($task->tags as $tag)
                            <span class="badge badge-xs font-medium"
                                  style="background-color: {{ $tag->color }}; color: #fff; border: none;">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Comments ─────────────────────────────────────────────────────── --}}
    <h2 class="text-lg font-semibold mb-4">
        Comments
        <span class="text-base-content/40 font-normal text-sm">({{ $task->comments->count() }})</span>
    </h2>

    {{-- Add comment form --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <form action="{{ route('comments.store', $task) }}" method="POST" class="space-y-4">
                @csrf

                {{-- Validation errors --}}
                @if($errors->any())
                    <div class="alert alert-error text-sm">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Title</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="100"
                           placeholder="Short summary..."
                           class="input input-bordered w-full {{ $errors->has('title') ? 'input-error' : '' }}">
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Comment <span class="text-base-content/40 font-normal">(optional)</span></span>
                    </label>
                    <textarea name="content" rows="3" maxlength="2000"
                              placeholder="Add details..."
                              class="textarea textarea-bordered w-full resize-none">{{ old('content') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
            </form>
        </div>
    </div>

    {{-- Comment list --}}
    @forelse($task->comments as $comment)
    <div class="card bg-base-100 shadow mb-3">
        <div class="card-body py-4 gap-1">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-2">
                    {{-- Avatar --}}
                    <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-full w-7 h-7 text-xs font-bold">
                            <span>{{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-sm leading-tight">{{ $comment->title }}</p>
                        <p class="text-xs text-base-content/50">
                            {{ $comment->user?->name ?? 'Unknown' }}
                            · {{ $comment->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Delete button — own comment or admin/manager --}}
                @if(auth()->id() === $comment->user_id || auth()->user()->hasAnyRole(['admin','manager']))
                <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                      onsubmit="return confirm('Delete this comment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                </form>
                @endif
            </div>

            @if($comment->content)
            <p class="text-sm text-base-content/80 mt-2 leading-relaxed pl-9">{{ $comment->content }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="card bg-base-100 shadow">
        <div class="card-body items-center py-12 text-center">
            <svg class="w-8 h-8 opacity-30 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p class="text-base-content/40 text-sm">No comments yet. Be the first!</p>
        </div>
    </div>
    @endforelse

</div>

@endsection
