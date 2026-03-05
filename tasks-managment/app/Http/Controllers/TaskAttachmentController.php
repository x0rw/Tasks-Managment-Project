<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $data = $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $file = $data['attachment'];
        $path = $file->store('task-attachments', 'public');

        $task->attachments()->create([
            'uploaded_by' => auth()->id(),
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('success', 'Attachment uploaded successfully.');
    }

    public function destroy(Project $project, Task $task, TaskAttachment $attachment)
    {
        if ($task->project_id !== $project->id || $attachment->task_id !== $task->id) {
            abort(404);
        }

        $user = auth()->user();
        if (! $user->hasAnyRole(['admin', 'manager']) && $attachment->uploaded_by !== $user->id) {
            abort(403, 'You are not allowed to delete this attachment.');
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment removed.');
    }
}
