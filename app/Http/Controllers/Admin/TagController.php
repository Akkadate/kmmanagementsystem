<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('articles')
            ->orderBy('name')
            ->get();

        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        $slug = \Illuminate\Support\Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (Tag::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Tag::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully!');
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ]);

        if ($validated['name'] !== $tag->name) {
            $slug = \Illuminate\Support\Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Tag::where('slug', $slug)->where('id', '!=', $tag->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $tag->slug = $slug;
        }

        $tag->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully!');
    }

    public function destroy(Tag $tag)
    {
        // Detach all articles before deleting
        $tag->articles()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully!');
    }
}
