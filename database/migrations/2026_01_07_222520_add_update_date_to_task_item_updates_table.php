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
        // Check if column already exists
        if (!Schema::hasColumn('task_item_updates', 'update_date')) {
            Schema::table('task_item_updates', function (Blueprint $table) {
                $table->date('update_date')->nullable()->after('updated_by');
            });
        }
        
        // Set default value untuk data existing
        \DB::table('task_item_updates')->whereNull('update_date')->update([
            'update_date' => \DB::raw('DATE(created_at)')
        ]);
        
        // Make it required after setting default (only if column exists and is nullable)
        if (Schema::hasColumn('task_item_updates', 'update_date')) {
            Schema::table('task_item_updates', function (Blueprint $table) {
                $table->date('update_date')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_item_updates', function (Blueprint $table) {
            $table->dropColumn('update_date');
        });
    }
};
