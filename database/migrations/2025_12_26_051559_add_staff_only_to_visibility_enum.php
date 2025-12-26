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
        // For PostgreSQL, we need to use raw SQL to alter enum type
        // Step 1: Drop the default temporarily
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility DROP DEFAULT;");

        // Step 2: Create new enum type with staff_only
        DB::statement("CREATE TYPE visibility_new AS ENUM ('public', 'members_only', 'staff_only', 'internal', 'private');");

        // Step 3: Convert column to new type
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility TYPE visibility_new USING visibility::text::visibility_new;");

        // Step 4: Drop old type (if exists) and rename new type
        DB::statement("DROP TYPE IF EXISTS visibility;");
        DB::statement("ALTER TYPE visibility_new RENAME TO visibility;");

        // Step 5: Restore default value
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility SET DEFAULT 'public'::visibility;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update any staff_only values to members_only before reverting
        DB::statement("UPDATE articles SET visibility = 'members_only' WHERE visibility = 'staff_only';");

        // Step 1: Drop default
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility DROP DEFAULT;");

        // Step 2: Revert to original enum values
        DB::statement("CREATE TYPE visibility_old AS ENUM ('public', 'members_only', 'internal', 'private');");
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility TYPE visibility_old USING visibility::text::visibility_old;");
        DB::statement("DROP TYPE IF EXISTS visibility;");
        DB::statement("ALTER TYPE visibility_old RENAME TO visibility;");

        // Step 3: Restore default
        DB::statement("ALTER TABLE articles ALTER COLUMN visibility SET DEFAULT 'public'::visibility;");
    }
};
