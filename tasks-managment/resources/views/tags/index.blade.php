@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Tags</h1>
        <p class="text-base-content/60 text-sm mt-1">Manage task labels across your workspace</p>
    </div>
</div>

{{-- Create Tag form (admin/manager only) --}}
@if(auth()->user()->hasAnyRole(['admin', 'manager']))
<div class="card bg-base-100 shadow mb-6">
    <div class="card-body">
        <h2 class="font-semibold text-base mb-3">New Tag</h2>
        <form action="{{ route('tags.store') }}" method="POST" class="flex items-end gap-3 flex-wrap">
            @csrf
            <div class="form-control flex-1 min-w-48">
                <label class="label"><span class="label-text font-medium">Name</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="50"
                       placeholder="e.g. Bug, Feature, Design..."
                       class="input input-bordered w-full {{ $errors->has('name') ? 'input-error' : '' }}">
                @error('name')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Colour</span></label>
                <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                       class="input input-bordered h-10 w-16 p-1 cursor-pointer">
                @error('color')<p class="text-error text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Add Tag</button>
        </form>
    </div>
</div>
@endif

{{-- Tags list --}}
<div class="card bg-base-100 shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-sm">
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Used on Tasks</th>
                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                    <th class="w-64">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($tags as $tag)
                <tr x-data="{ editing: false }">
                    {{-- Colour pill --}}
                    <td>
                        <span class="badge badge-sm font-medium"
                              style="background-color: {{ $tag->color }}; color: #fff; border: none;">
                            {{ $tag->name }}
                        </span>
                    </td>

                    {{-- Usage count --}}
                    <td class="text-base-content/60">{{ $tag->tasks_count }} {{ Str::plural('task', $tag->tasks_count) }}</td>

                    {{-- Edit / Delete (admin/manager) --}}
                    @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                    <td>
                        {{-- Default view: Edit + Delete buttons --}}
                        <div x-show="!editing" class="flex items-center gap-2">
                            <button @click="editing = true" class="btn btn-ghost btn-xs">Edit</button>
                            <form action="{{ route('tags.destroy', $tag) }}" method="POST"
                                  onsubmit="return confirm('Delete tag \'{{ $tag->name }}\'? It will be removed from all tasks.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-xs text-error">Delete</button>
                            </form>
                        </div>

                        {{-- Edit mode: inline form --}}
                        <form x-show="editing" x-cloak
                              action="{{ route('tags.update', $tag) }}" method="POST"
                              class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $tag->name }}" required maxlength="50"
                                   class="input input-bordered input-xs w-28">
                            <input type="color" name="color" value="{{ $tag->color }}"
                                   class="h-7 w-9 rounded cursor-pointer border border-base-300 p-0.5 bg-transparent">
                            <button type="submit" class="btn btn-primary btn-xs">Save</button>
                            <button type="button" @click="editing = false" class="btn btn-ghost btn-xs">Cancel</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-12 text-center text-base-content/40 text-sm">
                        No tags yet.
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                            Use the form above to create your first tag.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
