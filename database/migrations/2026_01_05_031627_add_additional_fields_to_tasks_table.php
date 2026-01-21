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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('factory')->nullable()->after('id');
            $table->string('project_code')->nullable()->after('factory');
            $table->enum('type', ['JOB DESCRIPTION', 'PROJECT', 'TASK', 'OTHER'])->default('TASK')->after('priority');
            $table->foreignId('requested_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->date('start_date')->nullable()->after('due_date');
            $table->string('file_support_1')->nullable()->after('start_date');
            $table->string('file_support_2')->nullable()->after('file_support_1');
            $table->integer('approve_level')->default(0)->after('file_support_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropColumn([
                'factory',
                'project_code',
                'type',
                'requested_by',
                'start_date',
                'file_support_1',
                'file_support_2',
                'approve_level',
            ]);
        });
    }
};
