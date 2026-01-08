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
        Schema::table('task_items', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('start_date');
            $table->time('due_time')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_items', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'due_time']);
        });
    }
};
