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
        Schema::create('task_item_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_item_id')->constrained('task_items')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->date('update_date');
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->integer('old_progress_percentage')->nullable();
            $table->integer('new_progress_percentage')->nullable();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_item_updates');
    }
};
