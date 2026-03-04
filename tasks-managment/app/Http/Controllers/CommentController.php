<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'title'   => 'required|string|max:100',
            'content' => 'nullable|string|max:2000',
        ]);

        $task->comments()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Comment $comment)
    {
        $user = auth()->user();

        if ($comment->user_id !== $user->id && ! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403, 'You can only delete your own comments.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
