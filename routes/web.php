<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use Illuminate\Support\Facades\Route;

// Suppress browser extension requests (WordPress REST API check)
Route::match(['GET', 'HEAD'], '/wp-json', function () {
    return response()->json(['message' => 'Not a WordPress site'], 404);
});

// Public routes
Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Profile
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/{article}', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // Article management - IMPORTANT: specific routes must come before wildcard routes
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article:slug}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::patch('/articles/{article:slug}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article:slug}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    // Feedback
    Route::post('/articles/{article}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // Admin Panel (admin and editor only)
    Route::prefix('admin')->name('admin.')->middleware('can:viewAny,App\Models\Article')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Tag Management
        Route::get('/tags', [AdminTagController::class, 'index'])->name('tags.index');
        Route::post('/tags', [AdminTagController::class, 'store'])->name('tags.store');
        Route::put('/tags/{tag}', [AdminTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [AdminTagController::class, 'destroy'])->name('tags.destroy');

        // Article Management
        Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
        Route::post('/articles/bulk-update', [AdminArticleController::class, 'bulkUpdate'])->name('articles.bulk-update');

        // Draft Approval (editors and admins only)
        Route::get('/drafts', [AdminArticleController::class, 'drafts'])->name('drafts.index');
        Route::patch('/drafts/{article}/approve', [AdminArticleController::class, 'approveDraft'])->name('drafts.approve');

        // User Management (admin only)
        Route::middleware('can:viewAny,App\Models\User')->group(function () {
            Route::resource('users', AdminUserController::class)->except(['show']);
        });

        // Analytics (editors and admins only)
        Route::get('/analytics', [AdminAnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
        Route::get('/analytics/top-articles', [AdminAnalyticsController::class, 'topArticles'])->name('analytics.top-articles');
        Route::get('/analytics/activity-logs', [AdminAnalyticsController::class, 'activityLogs'])->name('analytics.activity-logs');
        Route::get('/analytics/feedback', [AdminAnalyticsController::class, 'feedback'])->name('analytics.feedback');
    });
});

// Article show route - must come AFTER /articles/create to avoid conflict
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

require __DIR__.'/auth.php';
