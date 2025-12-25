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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('search_vector')->nullable();
        });

        DB::statement("ALTER TABLE articles ADD COLUMN search_vector_ts tsvector GENERATED ALWAYS AS (
            setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
            setweight(to_tsvector('english', coalesce(excerpt, '')), 'B') ||
            setweight(to_tsvector('english', coalesce(content, '')), 'C')
        ) STORED");

        DB::statement('CREATE INDEX articles_search_vector_idx ON articles USING GIN (search_vector_ts)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS articles_search_vector_idx');

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('search_vector');
            $table->dropColumn('search_vector_ts');
        });
    }
};
