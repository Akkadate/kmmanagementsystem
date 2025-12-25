<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function getMetrics(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        return [
            'total_articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'total_views' => Article::sum('view_count'),
            'articles_this_month' => Article::whereDate('created_at', '>=', $lastMonth)->count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_users' => User::count(),
        ];
    }

    public function getTopArticles(int $limit = 10): array
    {
        return Article::query()
            ->where('status', 'published')
            ->with(['author', 'category'])
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($article) {
                return [
                    'article' => $article,
                    'view_count' => $article->view_count,
                    'helpful_ratio' => $this->getArticleHelpfulRatio($article->id),
                ];
            })
            ->toArray();
    }

    public function getFeedbackStats(): array
    {
        $stats = Article::query()
            ->where('status', 'published')
            ->withCount([
                'feedback as helpful_count' => function ($query) {
                    $query->where('is_helpful', true);
                },
                'feedback as not_helpful_count' => function ($query) {
                    $query->where('is_helpful', false);
                },
                'feedback as total_feedback'
            ])
            ->having('total_feedback', '>', 0)
            ->orderBy('helpful_count', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($article) {
                $total = $article->total_feedback;
                $helpful = $article->helpful_count;

                return [
                    'article' => $article,
                    'helpful_count' => $helpful,
                    'not_helpful_count' => $article->not_helpful_count,
                    'total_feedback' => $total,
                    'helpful_percentage' => $total > 0 ? round(($helpful / $total) * 100, 1) : 0,
                ];
            })
            ->toArray();

        return $stats;
    }

    public function getArticleHelpfulRatio(int $articleId): ?float
    {
        $feedback = Feedback::where('article_id', $articleId)->get();

        if ($feedback->isEmpty()) {
            return null;
        }

        $helpful = $feedback->where('is_helpful', true)->count();
        $total = $feedback->count();

        return round(($helpful / $total) * 100, 1);
    }

    public function getActivityLogs(array $filters = [], int $perPage = 50)
    {
        $query = ActivityLog::query()
            ->with(['user', 'article'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by action type
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }

    public function getViewTrends(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        $trends = DB::table('articles')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as articles_created'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->where('created_at', '>=', $startDate)
            ->where('status', 'published')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        return $trends;
    }

    public function getCategoryStats(): array
    {
        return DB::table('categories')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(articles.id) as article_count'),
                DB::raw('SUM(articles.view_count) as total_views')
            )
            ->leftJoin('articles', function ($join) {
                $join->on('categories.id', '=', 'articles.category_id')
                    ->where('articles.status', '=', 'published');
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('article_count', 'desc')
            ->get()
            ->toArray();
    }

    public function getUserContributions(): array
    {
        $users = User::query()
            ->whereIn('role', ['admin', 'editor', 'contributor'])
            ->withCount([
                'articles as total_articles',
                'articles as published_articles' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->get()
            ->filter(function ($user) {
                return $user->total_articles > 0;
            })
            ->sortByDesc('published_articles')
            ->take(10)
            ->values();

        return $users->map(function ($user) {
            return [
                'user' => $user,
                'total_articles' => $user->total_articles,
                'published_articles' => $user->published_articles,
                'draft_articles' => $user->total_articles - $user->published_articles,
            ];
        })->toArray();
    }
}
