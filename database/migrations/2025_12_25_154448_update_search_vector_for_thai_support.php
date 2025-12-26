<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing search_vector_ts column and index
        DB::statement('DROP INDEX IF EXISTS articles_search_vector_idx');
        DB::statement('ALTER TABLE articles DROP COLUMN IF EXISTS search_vector_ts');

        // Create new search_vector_ts with 'simple' dictionary for Thai language support
        DB::statement("ALTER TABLE articles ADD COLUMN search_vector_ts tsvector GENERATED ALWAYS AS (
            setweight(to_tsvector('simple', coalesce(title, '')), 'A') ||
            setweight(to_tsvector('simple', coalesce(excerpt, '')), 'B') ||
            setweight(to_tsvector('simple', coalesce(content, '')), 'C')
        ) STORED");

        // Recreate the GIN index
        DB::statement('CREATE INDEX articles_search_vector_idx ON articles USING GIN (search_vector_ts)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the index and column
        DB::statement('DROP INDEX IF EXISTS articles_search_vector_idx');
        DB::statement('ALTER TABLE articles DROP COLUMN IF EXISTS search_vector_ts');

        // Restore the original English-only search_vector_ts
        DB::statement("ALTER TABLE articles ADD COLUMN search_vector_ts tsvector GENERATED ALWAYS AS (
            setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
            setweight(to_tsvector('english', coalesce(excerpt, '')), 'B') ||
            setweight(to_tsvector('english', coalesce(content, '')), 'C')
        ) STORED");

        // Recreate the GIN index
        DB::statement('CREATE INDEX articles_search_vector_idx ON articles USING GIN (search_vector_ts)');
    }
};
