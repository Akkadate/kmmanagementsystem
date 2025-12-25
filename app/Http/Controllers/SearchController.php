<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService
    ) {}

    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $filters = [
            'category_id' => $request->input('category'),
            'tag_ids' => $request->input('tags', []),
        ];

        $articles = $query || !empty(array_filter($filters))
            ? $this->searchService->search($query, $filters)->paginate(15)
            : collect();

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $tags = Tag::orderBy('name')->get();

        return view('search.results', compact('articles', 'categories', 'tags', 'query'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->autocomplete($query);

        return response()->json($suggestions);
    }
}
