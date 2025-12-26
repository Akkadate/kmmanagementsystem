<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'visibility',
        'department_id',
        'view_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ArticleVersion::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Visibility helper methods
    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isMembersOnly(): bool
    {
        return $this->visibility === 'members_only';
    }

    public function isInternal(): bool
    {
        return $this->visibility === 'internal';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function isStaffOnly(): bool
    {
        return $this->visibility === 'staff_only';
    }
}
