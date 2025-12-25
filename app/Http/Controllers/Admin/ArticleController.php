<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);

        $query = Article::query()->with(['author', 'category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('excerpt', 'ILIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $articles = $query->paginate(20)->withQueryString();

        // Get filter options
        $categories = Category::orderBy('name')->get();
        $authors = User::whereIn('role', ['admin', 'editor', 'contributor'])
            ->orderBy('name')
            ->get();

        return view('admin.articles.index', compact('articles', 'categories', 'authors'));
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('update', Article::class);

        $validated = $request->validate([
            'article_ids' => 'required|array',
            'article_ids.*' => 'exists:articles,id',
            'action' => 'required|in:publish,draft,delete',
        ]);

        $articles = Article::whereIn('id', $validated['article_ids'])->get();

        foreach ($articles as $article) {
            switch ($validated['action']) {
                case 'publish':
                    if (auth()->user()->can('update', $article)) {
                        $article->update([
                            'status' => 'published',
                            'published_at' => $article->published_at ?? now(),
                        ]);
                    }
                    break;
                case 'draft':
                    if (auth()->user()->can('update', $article)) {
                        $article->update(['status' => 'draft']);
                    }
                    break;
                case 'delete':
                    if (auth()->user()->can('delete', $article)) {
                        $article->delete();
                    }
                    break;
            }
        }

        $actionText = $validated['action'] === 'delete' ? 'deleted' : "set to {$validated['action']}";
        return redirect()->route('admin.articles.index')
            ->with('success', count($validated['article_ids']) . " articles {$actionText} successfully!");
    }
}
