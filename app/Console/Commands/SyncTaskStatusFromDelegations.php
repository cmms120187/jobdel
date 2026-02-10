<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class SyncTaskStatusFromDelegations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delegations:sync-task-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan status Task dengan status Delegasi (perbaiki task yang masih pending padahal delegasi sudah in_progress/accepted)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tasks = Task::with('delegations')->get();
        $updated = 0;

        foreach ($tasks as $task) {
            if ($task->delegations->isEmpty()) {
                continue;
            }

            $hasActive = $task->delegations->whereIn('status', ['accepted', 'in_progress'])->isNotEmpty();
            $allCompleted = $task->delegations->every(fn ($d) => $d->status === 'completed');

            if ($task->status === 'pending' && $hasActive) {
                $task->update(['status' => 'in_progress']);
                $this->line("Task #{$task->id} ({$task->title}): pending → in_progress");
                $updated++;
            } elseif ($task->status !== 'completed' && $allCompleted) {
                $task->update(['status' => 'completed']);
                $this->line("Task #{$task->id} ({$task->title}): {$task->status} → completed");
                $updated++;
            }
        }

        $this->info("Selesai. {$updated} task diperbarui.");
        return self::SUCCESS;
    }
}
