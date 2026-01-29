<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\TaskItem;
use App\Models\TaskItemUpdate;
use App\Models\User;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function overview(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        // default to current month if not provided
        $start = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
        $end = $request->query('end_date') ?: now()->endOfMonth()->toDateString();
        
        // Validate dates
        try {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            
            // Ensure start is before end
            if ($startDate->gt($endDate)) {
                $temp = $start;
                $start = $end;
                $end = $temp;
            }
        } catch (\Exception $e) {
            // If invalid, use default
            $start = now()->startOfMonth()->toDateString();
            $end = now()->endOfMonth()->toDateString();
        }

        // Get subordinate IDs (including self)
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->unique()->values();

        // Tasks for team (filtered by created_at)
        $teamQuery = Task::whereIn('created_by', $userIds)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        $teamCounts = $teamQuery->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $teamTotal = $teamQuery->count();

        // Tasks for current user
        $selfQuery = Task::where('created_by', $user->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        $selfCounts = $selfQuery->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $selfTotal = $selfQuery->count();

        // Prepare ordered status arrays for consistent charting in view
        $statusOrder = ['pending', 'in_progress', 'completed', 'cancelled'];

        $teamData = [];
        $selfData = [];
        foreach ($statusOrder as $s) {
            $teamData[] = (int) ($teamCounts->get($s, 0) ?? 0);
            $selfData[] = (int) ($selfCounts->get($s, 0) ?? 0);
        }

        return view('leader.reports.overview', compact('teamCounts', 'teamTotal', 'selfCounts', 'selfTotal', 'statusOrder', 'teamData', 'selfData', 'start', 'end'));
    }

    /**
     * Show list of tasks for team with filters, search and pagination
     */
    public function tasks(Request $request)
    {
        $user = Auth::user();
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->unique()->values();

    $q = $request->query('q');
    $status = $request->query('status');
    $start = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
    $end = $request->query('end_date') ?: now()->endOfMonth()->toDateString();

        $query = Task::with('creator', 'room')
            ->whereIn('created_by', $userIds)
            ->when($status, function($qBuilder) use ($status) {
                return $qBuilder->where('status', $status);
            })
            ->when($start, function($qBuilder) use ($start) {
                return $qBuilder->whereDate('created_at', '>=', $start);
            })
            ->when($end, function($qBuilder) use ($end) {
                return $qBuilder->whereDate('created_at', '<=', $end);
            })
            ->when($q, function($qBuilder) use ($q) {
                return $qBuilder->where(function($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('project_code', 'like', "%{$q}%")
                        ->orWhereHas('creator', function($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $tasks = $query->paginate(15)->withQueryString();

        return view('leader.reports.tasks', compact('tasks'));
    }

    /**
     * Export filtered tasks as CSV for leader team
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->unique()->values();

    $q = $request->query('q');
    $status = $request->query('status');
    $start = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
    $end = $request->query('end_date') ?: now()->endOfMonth()->toDateString();

        $query = Task::with('creator', 'room')
            ->whereIn('created_by', $userIds)
            ->when($status, function($qBuilder) use ($status) {
                return $qBuilder->where('status', $status);
            })
            ->when($start, function($qBuilder) use ($start) {
                return $qBuilder->whereDate('created_at', '>=', $start);
            })
            ->when($end, function($qBuilder) use ($end) {
                return $qBuilder->whereDate('created_at', '<=', $end);
            })
            ->when($q, function($qBuilder) use ($q) {
                return $qBuilder->where(function($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('project_code', 'like', "%{$q}%")
                        ->orWhereHas('creator', function($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc');

        $tasks = $query->get();

        $filename = 'team_tasks_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($tasks) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Project Code', 'Title', 'Creator', 'Status', 'Priority', 'Created At', 'Due Date']);

            foreach ($tasks as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->project_code,
                    $t->title,
                    optional($t->creator)->name,
                    $t->status,
                    $t->priority,
                    optional($t->created_at)->toDateTimeString(),
                    optional($t->due_date)->toDateString(),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show work time report for team - tasks in progress and time tracking per user
     */
    public function workTimeReport(Request $request)
    {
        $user = Auth::user();
        
        // Get date filter (default to today)
        $date = $request->query('date') ?: now()->toDateString();
        
        // Get subordinate IDs (including self)
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->unique()->values();

        // Get tasks in progress that have task items assigned to team members
        // Show tasks with status in_progress OR tasks that have task items assigned to team
        $tasksInProgress = Task::with(['taskItems' => function($query) use ($userIds, $date) {
                $query->whereIn('assigned_to', $userIds)
                      ->with(['assignedUser', 'updates' => function($q) use ($date) {
                          // Load all updates for the date, not just those with time
                          $q->whereDate('update_date', $date)
                            ->orderBy('time_from')
                            ->orderBy('created_at');
                      }]);
            }])
            ->where(function($q) use ($userIds) {
                // Task status is in_progress
                $q->where('status', 'in_progress')
                  // OR task has task items assigned to team members
                  ->orWhereHas('taskItems', function($query) use ($userIds) {
                      $query->whereIn('assigned_to', $userIds)
                            ->whereIn('status', ['pending', 'in_progress']);
                  });
            })
            ->whereHas('taskItems', function($query) use ($userIds) {
                // Must have at least one task item assigned to team
                $query->whereIn('assigned_to', $userIds);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate work time per user for the selected date
        $workTimePerUser = [];
        
        foreach ($subordinates as $subordinate) {
            // Get all task item updates for this user on the selected date
            // Include updates where user is assigned to task item OR user made the update
            // No longer filter by time_from/time_to - will use automatic calculation if not set
            $updates = TaskItemUpdate::whereDate('update_date', $date)
                ->where(function($q) use ($subordinate) {
                    // User made the update
                    $q->where('updated_by', $subordinate->id)
                      // OR user is assigned to the task item
                      ->orWhereHas('taskItem', function($query) use ($subordinate) {
                          $query->where('assigned_to', $subordinate->id);
                      });
                })
                ->with([
                    'taskItem.task.delegations', 
                    'taskItem.assignedUser',
                    'taskItem' => function($q) {
                        $q->with('task.delegations');
                    }
                ])
                ->get();

            $totalMinutes = 0;
            $taskDetails = [];

            foreach ($updates as $update) {
                // Always calculate duration, even if time_from/time_to are not set
                $duration = $update->duration_in_minutes;
                
                // Only include if duration is valid and positive
                if ($duration !== null && $duration > 0) {
                    $taskItem = $update->taskItem;
                    
                    // Check if this update belongs to the subordinate
                    // User made the update OR user is assigned to the task item
                    $belongsToUser = false;
                    if ($update->updated_by == $subordinate->id) {
                        $belongsToUser = true;
                    } elseif ($taskItem && $taskItem->assigned_to == $subordinate->id) {
                        $belongsToUser = true;
                    }
                    
                    if ($belongsToUser) {
                        $totalMinutes += $duration;
                        
                        $taskDetails[] = [
                            'task_title' => $taskItem ? ($taskItem->task->title ?? 'N/A') : 'N/A',
                            'task_item_title' => $taskItem ? $taskItem->title : 'N/A',
                            'time_from' => $update->time_from ?: $update->effective_time_from,
                            'time_to' => $update->time_to ?: $update->effective_time_to,
                            'duration' => $duration,
                            'formatted_duration' => $update->formatted_duration,
                            'notes' => $update->notes,
                            'is_auto_calculated' => !$update->time_from || !$update->time_to,
                        ];
                    }
                }
            }

            // Always include user in the list, even if no work time
            $workTimePerUser[$subordinate->id] = [
                'user' => $subordinate,
                'total_minutes' => $totalMinutes,
                'total_hours' => round($totalMinutes / 60, 2),
                'formatted_time' => $this->formatDuration($totalMinutes),
                'task_details' => $taskDetails,
                'task_count' => count($taskDetails),
            ];
        }

        // Sort by total minutes descending
        uasort($workTimePerUser, function($a, $b) {
            return $b['total_minutes'] <=> $a['total_minutes'];
        });

        // Calculate team totals
        $teamTotalMinutes = array_sum(array_column($workTimePerUser, 'total_minutes'));
        $teamTotalHours = round($teamTotalMinutes / 60, 2);
        $teamTotalTasks = array_sum(array_column($workTimePerUser, 'task_count'));

        return view('leader.reports.work-time', compact(
            'tasksInProgress',
            'workTimePerUser',
            'date',
            'teamTotalMinutes',
            'teamTotalHours',
            'teamTotalTasks',
            'subordinates'
        ));
    }

    /**
     * Show work time effectiveness report for a date range
     */
    public function workTimeHistory(Request $request)
    {
        $user = Auth::user();
        
        // Get date range filter (default to current week)
        $startDate = $request->query('start_date') ?: now()->startOfWeek()->toDateString();
        $endDate = $request->query('end_date') ?: now()->endOfWeek()->toDateString();
        
        // Validate dates
        try {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            
            if ($start->gt($end)) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
            }
        } catch (\Exception $e) {
            $startDate = now()->startOfWeek()->toDateString();
            $endDate = now()->endOfWeek()->toDateString();
        }
        
        // Get subordinate IDs (including self)
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->unique()->values();

        // Get work time per user per day for the date range
        $workTimeData = [];
        $dailyTotals = [];
        
        // Initialize dates array
        $currentDate = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        while ($currentDate->lte($end)) {
            $dateStr = $currentDate->toDateString();
            $dailyTotals[$dateStr] = [
                'date' => $dateStr,
                'formatted_date' => $currentDate->locale('id')->translatedFormat('l, d F Y'),
                'total_minutes' => 0,
                'total_hours' => 0,
                'user_count' => 0,
            ];
            $currentDate->addDay();
        }

        foreach ($subordinates as $subordinate) {
            $userData = [
                'user' => $subordinate,
                'daily_work_time' => [],
                'total_minutes' => 0,
                'total_hours' => 0,
                'average_minutes_per_day' => 0,
                'days_worked' => 0,
            ];

            // Get all updates for this user in the date range
            // Include updates where user is assigned to task item OR user made the update
            // No longer filter by time_from/time_to - will use automatic calculation if not set
            $updates = TaskItemUpdate::whereDate('update_date', '>=', $startDate)
                ->whereDate('update_date', '<=', $endDate)
                ->where(function($q) use ($subordinate) {
                    // User made the update
                    $q->where('updated_by', $subordinate->id)
                      // OR user is assigned to the task item
                      ->orWhereHas('taskItem', function($query) use ($subordinate) {
                          $query->where('assigned_to', $subordinate->id);
                      });
                })
                ->with(['taskItem.task', 'taskItem.assignedUser', 'taskItem.task.delegations'])
                ->get();

            // Group by date
            $dailyMinutes = [];
            foreach ($updates as $update) {
                $updateDate = \Carbon\Carbon::parse($update->update_date);
                $dateStr = $updateDate->format('Y-m-d');
                $duration = $update->duration_in_minutes;
                
                if ($duration !== null && $duration > 0) {
                    if (!isset($dailyMinutes[$dateStr])) {
                        $dailyMinutes[$dateStr] = 0;
                    }
                    $dailyMinutes[$dateStr] += $duration;
                    $userData['total_minutes'] += $duration;
                }
            }

            // Fill in daily work time
            $currentDate = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            while ($currentDate->lte($end)) {
                $dateStr = $currentDate->toDateString();
                $minutes = $dailyMinutes[$dateStr] ?? 0;
                
                $userData['daily_work_time'][$dateStr] = [
                    'minutes' => $minutes,
                    'hours' => round($minutes / 60, 2),
                    'formatted' => $this->formatDuration($minutes),
                ];
                
                if ($minutes > 0) {
                    $userData['days_worked']++;
                    $dailyTotals[$dateStr]['total_minutes'] += $minutes;
                    $dailyTotals[$dateStr]['user_count']++;
                }
                
                $currentDate->addDay();
            }

            $userData['total_hours'] = round($userData['total_minutes'] / 60, 2);
            $daysInRange = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
            $userData['average_minutes_per_day'] = $daysInRange > 0 ? round($userData['total_minutes'] / $daysInRange, 1) : 0;

            $workTimeData[$subordinate->id] = $userData;
        }

        // Calculate daily totals hours
        foreach ($dailyTotals as $date => &$total) {
            $total['total_hours'] = round($total['total_minutes'] / 60, 2);
            $total['formatted_time'] = $this->formatDuration($total['total_minutes']);
        }

        // Sort by total minutes descending
        uasort($workTimeData, function($a, $b) {
            return $b['total_minutes'] <=> $a['total_minutes'];
        });

        // Calculate overall totals
        $overallTotalMinutes = array_sum(array_column($workTimeData, 'total_minutes'));
        $overallTotalHours = round($overallTotalMinutes / 60, 2);
        $daysInRange = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        $averageMinutesPerDay = $daysInRange > 0 ? round($overallTotalMinutes / $daysInRange, 1) : 0;

        // Prepare data for chart
        $chartLabels = array_column($dailyTotals, 'formatted_date');
        $chartData = array_column($dailyTotals, 'total_hours');

        return view('leader.reports.work-time-history', compact(
            'workTimeData',
            'dailyTotals',
            'startDate',
            'endDate',
            'overallTotalMinutes',
            'overallTotalHours',
            'averageMinutesPerDay',
            'daysInRange',
            'chartLabels',
            'chartData',
            'subordinates'
        ));
    }

    /**
     * Show user reports list - all subordinates with summary statistics
     */
    public function userReports(Request $request)
    {
        $user = Auth::user();
        
        // Get date filter (default to current month)
        $start = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
        $end = $request->query('end_date') ?: now()->endOfMonth()->toDateString();
        
        // Validate dates
        try {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            
            if ($startDate->gt($endDate)) {
                $temp = $start;
                $start = $end;
                $end = $temp;
            }
        } catch (\Exception $e) {
            $start = now()->startOfMonth()->toDateString();
            $end = now()->endOfMonth()->toDateString();
        }
        
        // Get subordinate IDs (including self)
        $subordinates = $user->getSubordinatesIncludingSelf();
        
        // Calculate statistics for each user
        $userReports = [];
        
        foreach ($subordinates as $subordinate) {
            // Tasks created by this user
            $tasksCreated = Task::where('created_by', $subordinate->id)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
            
            $tasksCreatedCount = $tasksCreated->count();
            $tasksCreatedStats = [
                'pending' => (clone $tasksCreated)->where('status', 'pending')->count(),
                'in_progress' => (clone $tasksCreated)->where('status', 'in_progress')->count(),
                'completed' => (clone $tasksCreated)->where('status', 'completed')->count(),
                'cancelled' => (clone $tasksCreated)->where('status', 'cancelled')->count(),
            ];
            
            // Task items assigned to this user
            $taskItemsAssigned = TaskItem::where('assigned_to', $subordinate->id)
                ->whereHas('task', function($q) use ($start, $end) {
                    $q->whereDate('created_at', '>=', $start)
                      ->whereDate('created_at', '<=', $end);
                });
            
            $taskItemsCount = $taskItemsAssigned->count();
            $taskItemsStats = [
                'pending' => (clone $taskItemsAssigned)->where('status', 'pending')->count(),
                'in_progress' => (clone $taskItemsAssigned)->where('status', 'in_progress')->count(),
                'completed' => (clone $taskItemsAssigned)->where('status', 'completed')->count(),
            ];
            
            // Delegations received
            $delegationsReceived = \App\Models\Delegation::where('delegated_to', $subordinate->id)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
            
            $delegationsReceivedCount = $delegationsReceived->count();
            
            // Delegations given
            $delegationsGiven = \App\Models\Delegation::where('delegated_by', $subordinate->id)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end);
            
            $delegationsGivenCount = $delegationsGiven->count();
            
            // Total work time based on Delegation accepted_at and completed_at
            // Mulai kerja: ketika tombol "Terima Delegasi" diklik (accepted_at)
            // Selesai kerja: ketika tombol "Tandai Selesai" diklik, atau progress 100%, atau task item 100% (completed_at)
            // Jam istirahat: 12:00-13:00 WIB (tidak dihitung sebagai waktu kerja)
            $delegations = \App\Models\Delegation::where('delegated_to', $subordinate->id)
                ->whereNotNull('accepted_at')
                ->where(function($q) use ($start, $end) {
                    // Delegation yang accepted_at dalam periode filter
                    $q->whereDate('accepted_at', '>=', $start)
                      ->whereDate('accepted_at', '<=', $end);
                })
                ->with('task')
                ->get();
            
            $totalMinutes = 0;
            $workTimeByDate = [];
            
            foreach ($delegations as $delegation) {
                // Set timezone ke Asia/Jakarta (WIB)
                $acceptedAt = \Carbon\Carbon::parse($delegation->accepted_at)->setTimezone('Asia/Jakarta');
                $completedAt = $delegation->completed_at 
                    ? \Carbon\Carbon::parse($delegation->completed_at)->setTimezone('Asia/Jakarta')
                    : \Carbon\Carbon::now('Asia/Jakarta');
                
                // Hitung durasi dengan mengabaikan jam istirahat 12:00-13:00
                $durationMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $completedAt);
                
                if ($durationMinutes > 0) {
                    $totalMinutes += $durationMinutes;
                    
                    // Breakdown per hari: distribusikan durasi ke setiap hari dari accepted_at sampai completed_at
                    $currentDate = $acceptedAt->copy()->startOfDay();
                    $endDate = $completedAt->copy()->startOfDay();
                    
                    while ($currentDate->lte($endDate)) {
                        $dateStr = $currentDate->format('Y-m-d');
                        
                        if (!isset($workTimeByDate[$dateStr])) {
                            $workTimeByDate[$dateStr] = [
                                'date' => $dateStr,
                                'formatted_date' => $currentDate->locale('id')->translatedFormat('d M Y'),
                                'minutes' => 0,
                            ];
                        }
                        
                        // Hitung durasi untuk hari ini dengan mengabaikan jam istirahat
                        $dayStart = $currentDate->copy();
                        $dayEnd = $currentDate->copy()->endOfDay();
                        
                        if ($currentDate->format('Y-m-d') === $acceptedAt->format('Y-m-d') && 
                            $currentDate->format('Y-m-d') === $completedAt->format('Y-m-d')) {
                            // Same day: calculate actual duration excluding break
                            $dayMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $completedAt);
                        } elseif ($currentDate->format('Y-m-d') === $acceptedAt->format('Y-m-d')) {
                            // First day: from accepted_at to end of day
                            $dayMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $dayEnd);
                        } elseif ($currentDate->format('Y-m-d') === $completedAt->format('Y-m-d')) {
                            // Last day: from start of day to completed_at
                            $dayMinutes = $this->calculateWorkDurationExcludingBreak($dayStart, $completedAt);
                        } else {
                            // Middle days: full working day excluding break (8 hours = 480 minutes)
                            $dayMinutes = 480;
                        }
                        
                        $workTimeByDate[$dateStr]['minutes'] += $dayMinutes;
                        
                        $currentDate->addDay();
                    }
                }
            }
            
            // Sort by date descending
            krsort($workTimeByDate);
            
            // Calculate average minutes per day
            $daysWithWork = count($workTimeByDate);
            
            // Calculate total days in range for average calculation
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            $totalDaysInRange = $startDate->diffInDays($endDate) + 1;
            
            // Average based on days with work (not total days in range)
            // This gives the average productivity per working day
            $averageMinutesPerDay = $daysWithWork > 0 ? round($totalMinutes / $daysWithWork, 1) : 0;
            
            $userReports[] = [
                'user' => $subordinate,
                'tasks_created' => $tasksCreatedCount,
                'tasks_created_stats' => $tasksCreatedStats,
                'task_items_assigned' => $taskItemsCount,
                'task_items_stats' => $taskItemsStats,
                'delegations_received' => $delegationsReceivedCount,
                'delegations_given' => $delegationsGivenCount,
                'total_work_minutes' => $totalMinutes,
                'total_work_hours' => round($totalMinutes / 60, 2),
                'formatted_work_time' => $this->formatDuration($totalMinutes),
                'work_time_by_date' => array_values($workTimeByDate),
                'average_minutes_per_day' => $averageMinutesPerDay,
                'days_with_work' => $daysWithWork,
                'total_days_in_range' => $totalDaysInRange,
            ];
        }
        
        // Sort by total work minutes descending
        usort($userReports, function($a, $b) {
            return $b['total_work_minutes'] <=> $a['total_work_minutes'];
        });
        
        return view('leader.reports.user-reports', compact('userReports', 'start', 'end', 'subordinates'));
    }
    
    /**
     * Show detailed report for a specific user
     */
    public function userDetail(Request $request, $userId)
    {
        $user = Auth::user();
        
        // Get subordinate IDs (including self)
        $subordinates = $user->getSubordinatesIncludingSelf();
        $userIds = $subordinates->pluck('id')->toArray();
        
        // Validate that the requested user is in the subordinates list
        if (!in_array($userId, $userIds)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan user ini.');
        }
        
        $targetUser = User::with('position')->findOrFail($userId);
        
        // Get date filter (default to current month)
        $start = $request->query('start_date') ?: now()->startOfMonth()->toDateString();
        $end = $request->query('end_date') ?: now()->endOfMonth()->toDateString();
        
        // Validate dates
        try {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            
            if ($startDate->gt($endDate)) {
                $temp = $start;
                $start = $end;
                $end = $temp;
            }
        } catch (\Exception $e) {
            $start = now()->startOfMonth()->toDateString();
            $end = now()->endOfMonth()->toDateString();
        }
        
        // Tasks created by this user
        $tasksCreated = Task::with(['room', 'taskItems'])
            ->where('created_by', $targetUser->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $tasksCreatedStats = [
            'total' => $tasksCreated->count(),
            'pending' => $tasksCreated->where('status', 'pending')->count(),
            'in_progress' => $tasksCreated->where('status', 'in_progress')->count(),
            'completed' => $tasksCreated->where('status', 'completed')->count(),
            'cancelled' => $tasksCreated->where('status', 'cancelled')->count(),
        ];
        
        // Task items assigned to this user
        $taskItemsAssigned = TaskItem::with(['task.room', 'task.creator', 'updates'])
            ->where('assigned_to', $targetUser->id)
            ->whereHas('task', function($q) use ($start, $end) {
                $q->whereDate('created_at', '>=', $start)
                  ->whereDate('created_at', '<=', $end);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        $taskItemsStats = [
            'total' => $taskItemsAssigned->count(),
            'pending' => $taskItemsAssigned->where('status', 'pending')->count(),
            'in_progress' => $taskItemsAssigned->where('status', 'in_progress')->count(),
            'completed' => $taskItemsAssigned->where('status', 'completed')->count(),
        ];
        
        // Delegations received
        $delegationsReceived = \App\Models\Delegation::with(['task.room', 'task.creator', 'delegatedBy'])
            ->where('delegated_to', $targetUser->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Delegations given
        $delegationsGiven = \App\Models\Delegation::with(['task.room', 'task.creator', 'delegatedTo'])
            ->where('delegated_by', $targetUser->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Work time details based on Delegation accepted_at and completed_at
        // Mulai kerja: ketika tombol "Terima Delegasi" diklik (accepted_at)
        // Selesai kerja: ketika tombol "Tandai Selesai" diklik, atau progress 100%, atau task item 100% (completed_at)
        // Jam istirahat: 12:00-13:00 WIB (tidak dihitung sebagai waktu kerja)
        $delegations = \App\Models\Delegation::where('delegated_to', $targetUser->id)
            ->whereNotNull('accepted_at')
            ->where(function($q) use ($start, $end) {
                // Delegation yang accepted_at dalam periode filter
                $q->whereDate('accepted_at', '>=', $start)
                  ->whereDate('accepted_at', '<=', $end);
            })
            ->with(['task.room', 'task.creator'])
            ->orderBy('accepted_at', 'desc')
            ->get();
        
        $totalMinutes = 0;
        $workTimeByDate = [];
        
        foreach ($delegations as $delegation) {
            // Set timezone ke Asia/Jakarta (WIB)
            $acceptedAt = \Carbon\Carbon::parse($delegation->accepted_at)->setTimezone('Asia/Jakarta');
            $completedAt = $delegation->completed_at 
                ? \Carbon\Carbon::parse($delegation->completed_at)->setTimezone('Asia/Jakarta')
                : \Carbon\Carbon::now('Asia/Jakarta');
            
            // Hitung durasi dengan mengabaikan jam istirahat 12:00-13:00
            $durationMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $completedAt);
            
            if ($durationMinutes > 0) {
                $totalMinutes += $durationMinutes;
                
                // Breakdown per hari: distribusikan durasi ke setiap hari dari accepted_at sampai completed_at
                $currentDate = $acceptedAt->copy()->startOfDay();
                $endDate = $completedAt->copy()->startOfDay();
                
                while ($currentDate->lte($endDate)) {
                    $dateStr = $currentDate->format('Y-m-d');
                    
                    if (!isset($workTimeByDate[$dateStr])) {
                        $workTimeByDate[$dateStr] = [
                            'date' => $dateStr,
                            'formatted_date' => $currentDate->locale('id')->translatedFormat('l, d F Y'),
                            'minutes' => 0,
                            'delegations' => [],
                        ];
                    }
                    
                    // Hitung durasi untuk hari ini dengan mengabaikan jam istirahat
                    $dayStart = $currentDate->copy();
                    $dayEnd = $currentDate->copy()->endOfDay();
                    
                    if ($currentDate->format('Y-m-d') === $acceptedAt->format('Y-m-d') && 
                        $currentDate->format('Y-m-d') === $completedAt->format('Y-m-d')) {
                        // Same day: calculate actual duration excluding break
                        $dayMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $completedAt);
                    } elseif ($currentDate->format('Y-m-d') === $acceptedAt->format('Y-m-d')) {
                        // First day: from accepted_at to end of day
                        $dayMinutes = $this->calculateWorkDurationExcludingBreak($acceptedAt, $dayEnd);
                    } elseif ($currentDate->format('Y-m-d') === $completedAt->format('Y-m-d')) {
                        // Last day: from start of day to completed_at
                        $dayMinutes = $this->calculateWorkDurationExcludingBreak($dayStart, $completedAt);
                    } else {
                        // Middle days: full working day excluding break (8 hours = 480 minutes)
                        $dayMinutes = 480;
                    }
                    
                    $workTimeByDate[$dateStr]['minutes'] += $dayMinutes;
                    $workTimeByDate[$dateStr]['delegations'][] = [
                        'delegation' => $delegation,
                        'task_title' => $delegation->task->title,
                        'accepted_at' => $acceptedAt,
                        'completed_at' => $delegation->completed_at ? $completedAt : null,
                        'duration_minutes' => $dayMinutes,
                    ];
                    
                    $currentDate->addDay();
                }
            }
        }
        
        // Sort work time by date descending
        krsort($workTimeByDate);
        
        // Calculate average minutes per day
        $daysWithWork = count($workTimeByDate);
        $averageMinutesPerDay = $daysWithWork > 0 ? round($totalMinutes / $daysWithWork, 1) : 0;
        
        // Calculate total days in range
        $startDate = \Carbon\Carbon::parse($start);
        $endDate = \Carbon\Carbon::parse($end);
        $totalDaysInRange = $startDate->diffInDays($endDate) + 1;
        
        return view('leader.reports.user-detail', compact(
            'targetUser',
            'tasksCreated',
            'tasksCreatedStats',
            'taskItemsAssigned',
            'taskItemsStats',
            'delegationsReceived',
            'delegationsGiven',
            'workTimeByDate',
            'totalMinutes',
            'averageMinutesPerDay',
            'daysWithWork',
            'totalDaysInRange',
            'start',
            'end'
        ));
    }

    /**
     * Calculate work duration excluding break time (12:00-13:00 WIB)
     * 
     * @param \Carbon\Carbon $start
     * @param \Carbon\Carbon $end
     * @return int Duration in minutes
     */
    private function calculateWorkDurationExcludingBreak(\Carbon\Carbon $start, \Carbon\Carbon $end): int
    {
        // Ensure timezone is Asia/Jakarta (WIB)
        $start = $start->copy()->setTimezone('Asia/Jakarta');
        $end = $end->copy()->setTimezone('Asia/Jakarta');
        
        if ($start->gte($end)) {
            return 0;
        }
        
        $totalMinutes = 0;
        $current = $start->copy();
        
        while ($current->lt($end)) {
            // Set break time for current day: 12:00-13:00 WIB
            $breakStart = $current->copy()->setTime(12, 0, 0);
            $breakEnd = $current->copy()->setTime(13, 0, 0);
            
            // If current time is before break time today
            if ($current->lt($breakStart)) {
                // Calculate until break start or end time, whichever comes first
                $until = $breakStart->lt($end) ? $breakStart : $end;
                $totalMinutes += $current->diffInMinutes($until);
                $current = $until;
            }
            // If current time is during break time (12:00-13:00)
            elseif ($current->gte($breakStart) && $current->lt($breakEnd)) {
                // Skip break time - move to end of break
                $current = $breakEnd;
            }
            // If current time is after break time today
            else {
                // Calculate until end of day or end time, whichever comes first
                $endOfDay = $current->copy()->endOfDay();
                $until = $endOfDay->lt($end) ? $endOfDay : $end;
                $totalMinutes += $current->diffInMinutes($until);
                
                // Move to next day start
                $current = $current->copy()->addDay()->startOfDay();
                
                // If we've passed the end time, stop
                if ($current->gte($end)) {
                    break;
                }
            }
        }
        
        return $totalMinutes;
    }

    /**
     * Format duration in minutes to readable string
     */
    private function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . ' jam ' . $mins . ' menit';
        }
        return $mins . ' menit';
    }
}
