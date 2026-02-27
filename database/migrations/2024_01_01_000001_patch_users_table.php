<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Safe patch for the pre-existing users table from the legacy GageTrack app.
 *
 * Uses a raw SHOW COLUMNS query instead of Schema::hasColumn() to avoid
 * Windows MySQL case-sensitivity issues with information_schema lookups.
 *
 * This migration NEVER drops, renames, or modifies existing columns.
 */
return new class extends Migration
{
    /**
     * Check if a column exists using a raw SHOW COLUMNS query — reliable on
     * Windows MySQL where information_schema comparisons can be case-sensitive.
     */
    private function columnExists(string $table, string $column): bool
    {
        $results = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return count($results) > 0;
    }

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! $this->columnExists('users', 'emailAddress')) {
                $table->string('emailAddress', 100)->nullable()->after('username');
            }
        });
    }

    public function down(): void
    {
        if ($this->columnExists('users', 'emailAddress')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('emailAddress');
            });
        }
    }
};
