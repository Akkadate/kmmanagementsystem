<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'is_helpful' => 'required|boolean',
            'comment' => 'nullable|string|max:1000',
        ]);

        $feedback = Feedback::updateOrCreate(
            [
                'article_id' => $article->id,
                'user_id' => auth()->id(),
            ],
            [
                'is_helpful' => $validated['is_helpful'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'Thank you for your feedback!');
    }
}
