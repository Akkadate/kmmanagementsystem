<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()
            ->where('status', 'published')
            ->with(['author', 'category', 'tags'])
            ->orderBy('published_at', 'desc');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'ILIKE', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'ILIKE', "%{$searchTerm}%");
            });
        }

        $articles = $query->paginate(15);
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
        $tags = Tag::withCount('articles')->orderBy('name')->get();

        return view('articles.index', compact('articles', 'categories', 'tags'));
    }

    public function show(Article $article)
    {
        $this->authorize('view', $article);

        $article->increment('view_count');

        $article->load(['author', 'category', 'tags', 'versions' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(5);
        }]);

        $relatedArticles = $this->getRelatedArticles($article);

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    public function create()
    {
        $this->authorize('create', Article::class);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,members_only,staff_only,internal,private',
            'department_id' => 'nullable|exists:departments,id',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $slug = \Illuminate\Support\Str::slug($validated['title']);

        // If slug is empty (e.g., Thai characters), use a timestamp-based slug
        if (empty($slug)) {
            $slug = 'article-' . time();
        }

        $originalSlug = $slug;
        $counter = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $article = Article::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'author_id' => auth()->id(),
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'department_id' => $validated['department_id'] ?? null,
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (isset($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('articles', 'public');
            $article->update(['featured_image' => $path]);
        }

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article created successfully!');
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $article->load('tags');

        return view('articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,members_only,staff_only,internal,private',
            'department_id' => 'nullable|exists:departments,id',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        if ($validated['title'] !== $article->title) {
            $slug = \Illuminate\Support\Str::slug($validated['title']);

            // If slug is empty (e.g., Thai characters), use a timestamp-based slug
            if (empty($slug)) {
                $slug = 'article-' . time();
            }

            $originalSlug = $slug;
            $counter = 1;

            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $article->slug = $slug;
        }

        $article->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'department_id' => $validated['department_id'] ?? null,
            'published_at' => $validated['status'] === 'published' && !$article->published_at ? now() : $article->published_at,
        ]);

        if (isset($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        } else {
            $article->tags()->detach();
        }

        // Handle featured image removal
        if ($request->input('remove_featured_image')) {
            if ($article->featured_image) {
                \Storage::disk('public')->delete($article->featured_image);
                $article->update(['featured_image' => null]);
            }
        }

        // Handle new featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($article->featured_image) {
                \Storage::disk('public')->delete($article->featured_image);
            }

            $path = $request->file('featured_image')->store('articles', 'public');
            $article->update(['featured_image' => $path]);
        }

        return redirect()->route('articles.show', $article->slug)
            ->with('success', 'Article updated successfully!');
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Article deleted successfully!');
    }

    private function getRelatedArticles(Article $article, int $limit = 5)
    {
        $tagIds = $article->tags->pluck('id');

        return Article::query()
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->where(function ($query) use ($article, $tagIds) {
                $query->where('category_id', $article->category_id)
                    ->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
            })
            ->with(['author', 'category'])
            ->limit($limit)
            ->get();
    }
}
