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
        // Check status first - drafts only visible to author/editors/admins
        if ($article->status === 'draft') {
            if (!$user) {
                return false;
            }
            return $user->isAdmin() || $user->isEditor() || $user->id === $article->author_id;
        }

        // Check department restrictions
        if (!$this->canViewDepartment($user, $article)) {
            return false;
        }

        // For published articles, check visibility level
        switch ($article->visibility) {
            case 'public':
                // Check if category is public for guest users
                if (!$user && $article->category) {
                    return $article->category->is_public;
                }
                return true;

            case 'members_only':
                // Requires authentication (any logged-in user, even without department)
                return $user !== null;

            case 'staff_only':
                // Requires authentication AND must have a department (internal staff only)
                if (!$user) {
                    return false;
                }
                return $user->department_id !== null;

            case 'internal':
                // Only staff members (admin, editor, contributor)
                if (!$user) {
                    return false;
                }
                return in_array($user->role, ['admin', 'editor', 'contributor']);

            case 'private':
                // Only author and admins
                if (!$user) {
                    return false;
                }
                return $user->isAdmin() || $user->id === $article->author_id;

            default:
                return false;
        }
    }

    /**
     * Check if user can view article based on department restriction.
     */
    protected function canViewDepartment(?User $user, Article $article): bool
    {
        // If article has no department restriction, allow
        if (!$article->department_id) {
            return true;
        }

        // Admins can see all departments
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Check if user is in the same department
        if ($user && $user->department_id === $article->department_id) {
            return true;
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
