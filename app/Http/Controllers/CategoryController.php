<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->withCount('articles')
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        $category->load(['children' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        $articles = $category->articles()
            ->where('status', 'published')
            ->with(['author', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        $subcategoryIds = $category->descendants()->pluck('id');
        $descendantArticlesCount = \App\Models\Article::query()
            ->where('status', 'published')
            ->whereIn('category_id', $subcategoryIds)
            ->count();

        return view('categories.show', compact('category', 'articles', 'descendantArticlesCount'));
    }
}
