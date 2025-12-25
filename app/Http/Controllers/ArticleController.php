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
