<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /** List all tags — visible to all auth users */
    public function index()
    {
        $tags = Tag::withCount('tasks')->latest()->get();
        return view('tags.index', compact('tags'));
    }

    /** Create a new tag (admin/manager only — enforced by route middleware) */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:50|unique:tags,name',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Tag::create($request->only('name', 'color'));

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    /** Update an existing tag (admin/manager only) */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name'  => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag->update($request->only('name', 'color'));

        return redirect()->route('tags.index')->with('success', 'Tag updated.');
    }

    /** Delete a tag (admin/manager only) */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted.');
    }
}
