<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Delegation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        if ($isSuperuser) {
            // Superuser can see all tasks and delegations
            $myTasks = Task::with('delegations.delegatedTo.position')
                ->latest()
                ->get();
            
            $delegatedToMe = Delegation::with(['task', 'delegatedBy.position'])
                ->latest()
                ->get();
            
            $delegatedByMe = Delegation::with(['task', 'delegatedTo.position'])
                ->latest()
                ->get();
            
            // Statistics for all data
            $stats = [
                'total_tasks' => Task::count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'in_progress_tasks' => Task::where('status', 'in_progress')->count(),
                'completed_tasks' => Task::where('status', 'completed')->count(),
                'my_delegations' => Delegation::count(),
                'pending_delegations' => Delegation::where('status', 'pending')->count(),
            ];
        } else {
            // Regular user sees only their own data
            $myTasks = Task::where('created_by', $user->id)
                ->with('delegations.delegatedTo.position')
                ->latest()
                ->get();
            
            $delegatedToMe = Delegation::where('delegated_to', $user->id)
                ->with(['task', 'delegatedBy.position'])
                ->latest()
                ->get();
            
            $delegatedByMe = Delegation::where('delegated_by', $user->id)
                ->with(['task', 'delegatedTo.position'])
                ->latest()
                ->get();
            
            // Statistics
            $stats = [
                'total_tasks' => Task::where('created_by', $user->id)->count(),
                'pending_tasks' => Task::where('created_by', $user->id)->where('status', 'pending')->count(),
                'in_progress_tasks' => Task::where('created_by', $user->id)->where('status', 'in_progress')->count(),
                'completed_tasks' => Task::where('created_by', $user->id)->where('status', 'completed')->count(),
                'my_delegations' => Delegation::where('delegated_to', $user->id)->count(),
                'pending_delegations' => Delegation::where('delegated_to', $user->id)->where('status', 'pending')->count(),
            ];
        }
        
        return view('dashboard', compact('myTasks', 'delegatedToMe', 'delegatedByMe', 'stats', 'isSuperuser'));
    }
}
