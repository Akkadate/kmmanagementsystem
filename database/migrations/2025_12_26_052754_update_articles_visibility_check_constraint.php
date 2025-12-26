<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old check constraint
        DB::statement('ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_visibility_check;');

        // Create new check constraint with staff_only included
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_visibility_check CHECK (visibility IN ('public', 'members_only', 'staff_only', 'internal', 'private'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint with staff_only
        DB::statement('ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_visibility_check;');

        // Restore original constraint without staff_only
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_visibility_check CHECK (visibility IN ('public', 'members_only', 'internal', 'private'));");
    }
};
