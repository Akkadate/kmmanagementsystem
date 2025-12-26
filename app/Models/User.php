<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isContributor(): bool
    {
        return $this->role === 'contributor';
    }

    public function canEditArticle(Article $article): bool
    {
        return $this->isAdmin() || $this->isEditor() || ($this->isContributor() && $this->id === $article->author_id);
    }

    public function canPublishArticle(): bool
    {
        return $this->isAdmin() || $this->isEditor();
    }
}
