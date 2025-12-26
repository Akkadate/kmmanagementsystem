<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::withCount('articles')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $slug = \Illuminate\Support\Str::slug($validated['name']);

        // If slug is empty (e.g., Thai characters), use a timestamp-based slug
        if (empty($slug)) {
            $slug = 'category-' . time();
        }

        $originalSlug = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);

        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Prevent circular reference
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return back()->withErrors(['parent_id' => 'A category cannot be its own parent.']);
        }

        // Prevent setting a child as parent
        if (isset($validated['parent_id'])) {
            $descendantIds = $this->getDescendantIds($category);
            if (in_array($validated['parent_id'], $descendantIds)) {
                return back()->withErrors(['parent_id' => 'Cannot set a descendant category as parent.']);
            }
        }

        if ($validated['name'] !== $category->name) {
            $slug = \Illuminate\Support\Str::slug($validated['name']);

            // If slug is empty (e.g., Thai characters), use a timestamp-based slug
            if (empty($slug)) {
                $slug = 'category-' . time();
            }

            $originalSlug = $slug;
            $counter = 1;

            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $category->slug = $slug;
        }

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Check if category has articles
        if ($category->articles()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with existing articles. Please reassign articles first.']);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with subcategories. Please delete or reassign subcategories first.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    private function getDescendantIds(Category $category): array
    {
        $descendants = [];
        $children = $category->children;

        foreach ($children as $child) {
            $descendants[] = $child->id;
            $descendants = array_merge($descendants, $this->getDescendantIds($child));
        }

        return $descendants;
    }
}
