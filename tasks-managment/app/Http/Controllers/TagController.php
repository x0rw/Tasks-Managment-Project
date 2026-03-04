<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('tasks')->latest()->get();
        return view('tags.index', compact('tags'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:50|unique:tags,name',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Tag::create($request->only('name', 'color'));

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name'  => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag->update($request->only('name', 'color'));

        return redirect()->route('tags.index')->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted.');
    }
}
