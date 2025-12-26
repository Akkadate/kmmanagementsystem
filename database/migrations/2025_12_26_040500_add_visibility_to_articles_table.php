<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'members_only', 'internal', 'private'])
                  ->default('public')
                  ->after('status');
            $table->foreignId('department_id')
                  ->nullable()
                  ->after('visibility')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['visibility', 'department_id']);
        });
    }
};
