<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bookmarks = auth()->user()
            ->bookmarks()
            ->with(['article.category', 'article.author'])
            ->latest()
            ->paginate(20);

        return view('profile.bookmarks', compact('bookmarks'));
    }

    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
        ]);

        // Check if already bookmarked
        $existing = Bookmark::where('user_id', auth()->id())
            ->where('article_id', $validated['article_id'])
            ->first();

        if ($existing) {
            // Already bookmarked, remove it (toggle off)
            $existing->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'bookmarked' => false,
                    'message' => 'Bookmark removed',
                ]);
            }

            return redirect()->back()->with('success', 'Bookmark removed successfully!');
        }

        // Not bookmarked yet, create it (toggle on)
        Bookmark::create([
            'user_id' => auth()->id(),
            'article_id' => $validated['article_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'bookmarked' => true,
                'message' => 'Article bookmarked',
            ]);
        }

        return redirect()->back()->with('success', 'Article bookmarked successfully!');
    }

    public function destroy(Bookmark $bookmark)
    {
        // Ensure user can only delete their own bookmarks
        if ($bookmark->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $bookmark->delete();

        return redirect()->back()->with('success', 'Bookmark removed successfully!');
    }
}
