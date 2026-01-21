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
        // Check if columns already exist
        if (!Schema::hasColumn('task_item_updates', 'time_from')) {
            Schema::table('task_item_updates', function (Blueprint $table) {
                $table->time('time_from')->nullable()->after('update_date');
            });
        }
        
        if (!Schema::hasColumn('task_item_updates', 'time_to')) {
            Schema::table('task_item_updates', function (Blueprint $table) {
                $table->time('time_to')->nullable()->after('time_from');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_item_updates', function (Blueprint $table) {
            $table->dropColumn(['time_from', 'time_to']);
        });
    }
};
