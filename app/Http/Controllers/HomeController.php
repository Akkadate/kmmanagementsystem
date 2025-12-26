<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured/top articles
        $featuredArticles = Article::query()
            ->where('status', 'published')
            ->with(['author', 'category'])
            ->orderBy('view_count', 'desc')
            ->limit(6)
            ->get();

        // Get recent articles
        $recentArticles = Article::query()
            ->where('status', 'published')
            ->with(['author', 'category'])
            ->orderBy('published_at', 'desc')
            ->limit(8)
            ->get();

        // Get top categories with article count
        $topCategories = Category::query()
            ->withCount(['articles' => function ($query) {
                $query->where('status', 'published');
            }])
            ->get()
            ->filter(function ($category) {
                return $category->articles_count > 0;
            })
            ->sortByDesc('articles_count')
            ->take(8)
            ->values();

        // Get statistics
        $stats = [
            'total_articles' => Article::where('status', 'published')->count(),
            'total_categories' => Category::count(),
            'total_views' => Article::sum('view_count'),
        ];

        return view('home', compact('featuredArticles', 'recentArticles', 'topCategories', 'stats'));
    }
}
