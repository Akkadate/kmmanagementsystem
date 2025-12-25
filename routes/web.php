<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Article management (for contributors, editors, admins)
    Route::middleware('can:create,App\Models\Article')->group(function () {
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    });

    Route::middleware('can:update,article')->group(function () {
        Route::get('/articles/{article:slug}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::patch('/articles/{article:slug}', [ArticleController::class, 'update'])->name('articles.update');
    });

    Route::middleware('can:delete,article')->group(function () {
        Route::delete('/articles/{article:slug}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    });

    // Feedback
    Route::post('/articles/{article}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

require __DIR__.'/auth.php';
