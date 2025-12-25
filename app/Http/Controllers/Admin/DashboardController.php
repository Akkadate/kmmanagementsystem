<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Article::class);

        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'total_views' => Article::sum('view_count'),
        ];

        $recent_articles = Article::with('author')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $top_categories = Category::withCount([
            'articles',
            'articles as published_articles_count' => function ($query) {
                $query->where('status', 'published');
            }
        ])
            ->orderBy('articles_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_articles', 'top_categories'));
    }
}
