<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public function search(string $query, array $filters = []): Builder
    {
        $searchQuery = Article::query()
            ->where('status', 'published')
            ->with(['author', 'category', 'tags']);

        if (!empty($query)) {
            $searchQuery->whereRaw(
                "search_vector_ts @@ plainto_tsquery('simple', ?)",
                [$query]
            )->orderByRaw(
                "ts_rank(search_vector_ts, plainto_tsquery('simple', ?)) DESC",
                [$query]
            );
        }

        if (!empty($filters['category_id'])) {
            $searchQuery->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['tag_ids'])) {
            $searchQuery->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('tags.id', $filters['tag_ids']);
            });
        }

        if (!empty($filters['author_id'])) {
            $searchQuery->where('author_id', $filters['author_id']);
        }

        return $searchQuery;
    }

    public function autocomplete(string $query, int $limit = 5): array
    {
        return Article::query()
            ->where('status', 'published')
            ->whereRaw(
                "search_vector_ts @@ plainto_tsquery('simple', ?)",
                [$query]
            )
            ->orderByRaw(
                "ts_rank(search_vector_ts, plainto_tsquery('simple', ?)) DESC",
                [$query]
            )
            ->limit($limit)
            ->pluck('title')
            ->toArray();
    }
}
