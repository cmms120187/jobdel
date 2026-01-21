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
        if (!Schema::hasColumn('users', 'leader_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('leader_id')->nullable()->after('position_id')->constrained('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'leader_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['leader_id']);
                $table->dropColumn('leader_id');
            });
        }
    }
};
