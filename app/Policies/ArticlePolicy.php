<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Article $article): bool
    {
        if ($article->status === 'published') {
            return true;
        }

        if ($user) {
            return $user->isAdmin() || $user->isEditor() || $user->id === $article->author_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor', 'contributor']) && $user->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        if (!$user->is_active) {
            return false;
        }

        return $user->canEditArticle($article);
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Article $article): bool
    {
        if (!$user->is_active) {
            return false;
        }

        return $user->canPublishArticle();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        if (!$user->is_active) {
            return false;
        }

        return $user->isAdmin() || $user->isEditor() || ($user->isContributor() && $user->id === $article->author_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        return $user->isAdmin();
    }
}
