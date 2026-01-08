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
        // Create rooms table first (required for tasks.room_id foreign key)
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room')->unique();
            $table->string('plant');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->string('project_code')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('type', ['JOB DESCRIPTION', 'PROJECT', 'TASK', 'OTHER'])->default('TASK');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->string('file_support_1')->nullable();
            $table->string('file_support_2')->nullable();
            $table->integer('approve_level')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('add_request')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('rooms');
    }
};
