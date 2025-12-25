<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\User;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function dashboard()
    {
        // Only admins and editors can access analytics
        if (!in_array(auth()->user()->role, ['admin', 'editor'])) {
            abort(403, 'Only admins and editors can access analytics.');
        }

        $metrics = $this->analyticsService->getMetrics();
        $topArticles = $this->analyticsService->getTopArticles(5);
        $viewTrends = $this->analyticsService->getViewTrends(30);
        $categoryStats = $this->analyticsService->getCategoryStats();
        $userContributions = $this->analyticsService->getUserContributions();

        return view('admin.analytics.dashboard', compact(
            'metrics',
            'topArticles',
            'viewTrends',
            'categoryStats',
            'userContributions'
        ));
    }

    public function topArticles()
    {
        // Only admins and editors can access analytics
        if (!in_array(auth()->user()->role, ['admin', 'editor'])) {
            abort(403, 'Only admins and editors can access analytics.');
        }

        $topArticles = $this->analyticsService->getTopArticles(50);

        return view('admin.analytics.top-articles', compact('topArticles'));
    }

    public function activityLogs(Request $request)
    {
        // Only admins and editors can access analytics
        if (!in_array(auth()->user()->role, ['admin', 'editor'])) {
            abort(403, 'Only admins and editors can access analytics.');
        }

        $filters = $request->only(['user_id', 'action', 'date_from', 'date_to']);
        $activityLogs = $this->analyticsService->getActivityLogs($filters, 50);

        $users = User::orderBy('name')->get();
        $actions = ['created', 'updated', 'published', 'deleted', 'viewed'];

        return view('admin.analytics.activity-logs', compact('activityLogs', 'users', 'actions', 'filters'));
    }

    public function feedback()
    {
        // Only admins and editors can access analytics
        if (!in_array(auth()->user()->role, ['admin', 'editor'])) {
            abort(403, 'Only admins and editors can access analytics.');
        }

        $feedbackStats = $this->analyticsService->getFeedbackStats();

        return view('admin.analytics.feedback', compact('feedbackStats'));
    }
}
