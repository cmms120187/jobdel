<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display project management report with timeline
     */
    public function timeline(Request $request)
    {
        $user = Auth::user();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        // Get filter parameters
        $roomId = $request->get('room_id');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $filterUserId = $request->get('user_id');
        $groupBy = $request->get('group_by', 'daily'); // daily | weekly | monthly
        if (!in_array($groupBy, ['daily', 'weekly', 'monthly'])) {
            $groupBy = 'daily';
        }
        
        // Get subordinates for filter dropdown
        $subordinates = $user->getSubordinatesIncludingSelf();
        
        // Validate filter user_id - user must be able to see that user (subordinate or self)
        if ($filterUserId) {
            $allowedUserIds = $subordinates->pluck('id')->toArray();
            if (!in_array($filterUserId, $allowedUserIds)) {
                $filterUserId = null; // Reset if not allowed
            }
        }
        
        // Base query for task items
        $query = TaskItem::with(['task.room', 'task.creator', 'assignedUser.position', 'task.delegations.delegatedTo', 'updates'])
            ->whereHas('task', function($q) use ($isSuperuser, $user, $roomId, $status, $filterUserId, $subordinates) {
                // User filter logic
                if ($isSuperuser) {
                    // Superuser can see all tasks
                    if ($filterUserId) {
                        // Filter by specific user
                        $q->where(function($query) use ($filterUserId) {
                            $query->where('created_by', $filterUserId)
                                  ->orWhereHas('delegations', function($dq) use ($filterUserId) {
                                      $dq->where('delegated_to', $filterUserId);
                                  });
                        });
                    }
                } else {
                    if ($filterUserId) {
                        // Filter by specific subordinate user
                        $q->where(function($query) use ($filterUserId) {
                            $query->where('created_by', $filterUserId)
                                  ->orWhereHas('delegations', function($dq) use ($filterUserId) {
                                      $dq->where('delegated_to', $filterUserId);
                                  });
                        });
                    } else {
                        // Show tasks created by user OR tasks delegated to user OR tasks of subordinates
                        $subordinateIds = $subordinates->pluck('id')->toArray();
                        
                        $q->where(function($query) use ($user, $subordinateIds) {
                            // User's own tasks
                            $query->where('created_by', $user->id)
                                  ->orWhereHas('delegations', function($dq) use ($user) {
                                      $dq->where('delegated_to', $user->id);
                                  })
                                  // Subordinates' tasks
                                  ->orWhere(function($sq) use ($subordinateIds) {
                                      $sq->whereIn('created_by', $subordinateIds)
                                        ->orWhereHas('delegations', function($dq) use ($subordinateIds) {
                                            $dq->whereIn('delegated_to', $subordinateIds);
                                        });
                                  });
                        });
                    }
                }
                
                if ($roomId) {
                    $q->where('room_id', $roomId);
                }
                
                if ($status) {
                    $q->where('status', $status);
                }
            });
        
        // Apply date filters
        if ($dateFrom) {
            $query->where(function($q) use ($dateFrom) {
                $q->whereNotNull('due_date')
                  ->where('due_date', '>=', $dateFrom)
                  ->orWhereHas('task', function($tq) use ($dateFrom) {
                      $tq->whereNotNull('start_date')
                         ->where('start_date', '>=', $dateFrom);
                  });
            });
        }
        
        if ($dateTo) {
            $query->where(function($q) use ($dateTo) {
                $q->whereNotNull('due_date')
                  ->where('due_date', '<=', $dateTo)
                  ->orWhereHas('task', function($tq) use ($dateTo) {
                      $tq->whereNotNull('start_date')
                         ->where('start_date', '<=', $dateTo);
                  });
            });
        }
        
        $taskItems = $query->orderBy('order')->orderBy('id')->get();
        
        // Calculate date range for timeline
        $minDate = null;
        $maxDate = null;
        
        foreach ($taskItems as $item) {
            $task = $item->task;
            // Use task item's start_date and due_date first, then fallback to task dates
            $startDate = $item->start_date
                ? Carbon::parse($item->start_date)
                : ($item->due_date
                    ? Carbon::parse($item->due_date)->copy()->subDays(7)
                    : ($task->start_date ? Carbon::parse($task->start_date) : Carbon::today()));
            $endDate = $item->due_date
                ? Carbon::parse($item->due_date)
                : ($item->start_date
                    ? Carbon::parse($item->start_date)->copy()->addDays(7)
                    : ($task->due_date ? Carbon::parse($task->due_date) : $startDate->copy()->addDays(7)));
            
            if (!$minDate || $startDate < $minDate) {
                $minDate = $startDate->copy();
            }
            if (!$maxDate || $endDate > $maxDate) {
                $maxDate = $endDate->copy();
            }
        }
        
        // If no data, use default range
        if (!$minDate || !$maxDate) {
            $minDate = Carbon::today()->subDays(30);
            $maxDate = Carbon::today()->addDays(30);
        } else {
            // Extend range a bit before and after
            $minDate->subDays(7);
            $maxDate->addDays(7);
        }
        
        // Generate date range / period columns based on group_by
        $dateRange = [];
        if ($groupBy === 'daily') {
            $current = $minDate->copy();
            while ($current <= $maxDate) {
                $dateRange[] = [
                    'key' => $current->format('Y-m-d'),
                    'label' => $current->format('d'),
                    'sublabel' => $current->format('M'),
                    'start' => $current->copy()->startOfDay(),
                    'end' => $current->copy()->endOfDay(),
                ];
                $current->addDay();
            }
            // Limit to max 90 columns for daily
            if (count($dateRange) > 90) {
                $today = Carbon::today();
                $start = $today->copy()->subDays(45);
                $end = $today->copy()->addDays(45);
                $dateRange = [];
                $current = $start->copy();
                while ($current <= $end) {
                    $dateRange[] = [
                        'key' => $current->format('Y-m-d'),
                        'label' => $current->format('d'),
                        'sublabel' => $current->format('M'),
                        'start' => $current->copy()->startOfDay(),
                        'end' => $current->copy()->endOfDay(),
                    ];
                    $current->addDay();
                }
            }
        } elseif ($groupBy === 'weekly') {
            // Start from Monday of the week containing minDate
            $current = $minDate->copy()->startOfWeek(Carbon::MONDAY);
            while ($current <= $maxDate) {
                $weekEnd = $current->copy()->endOfWeek(Carbon::MONDAY);
                $weekNum = $current->weekOfYear;
                $monthShort = $current->format('M');
                $dateRange[] = [
                    'key' => $current->format('Y-\WW') . str_pad($weekNum, 2, '0', STR_PAD_LEFT),
                    'label' => 'W' . $weekNum,
                    'sublabel' => $monthShort,
                    'start' => $current->copy(),
                    'end' => $weekEnd,
                ];
                $current->addWeek()->startOfWeek(Carbon::MONDAY);
            }
            // Limit to max 52 columns for weekly (~1 year)
            if (count($dateRange) > 52) {
                $dateRange = array_slice($dateRange, 0, 52);
            }
        } else {
            // monthly
            $current = $minDate->copy()->startOfMonth();
            while ($current <= $maxDate) {
                $monthEnd = $current->copy()->endOfMonth();
                $dateRange[] = [
                    'key' => $current->format('Y-m'),
                    'label' => $current->format('M'),
                    'sublabel' => $current->format('Y'),
                    'start' => $current->copy(),
                    'end' => $monthEnd,
                ];
                $current->addMonth()->startOfMonth();
            }
            // Limit to max 24 columns for monthly (~2 years)
            if (count($dateRange) > 24) {
                $dateRange = array_slice($dateRange, 0, 24);
            }
        }
        
        // Group data by task
        $groupedData = [];
        foreach ($taskItems as $item) {
            $task = $item->task;
            $taskId = $task->id;
            
            if (!isset($groupedData[$taskId])) {
                // Determine PIC for task (from delegations)
                $taskPic = 'Tidak ada';
                if ($task->delegations->isNotEmpty()) {
                    $delegation = $task->delegations->first();
                    $taskPic = $delegation->delegatedTo->name;
                } elseif ($task->creator) {
                    $taskPic = $task->creator->name;
                }
                
                $groupedData[$taskId] = [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                    'task_code' => $task->project_code,
                    'task_description' => $task->description,
                    'task_room' => $task->room ? $task->room->room . ' (' . $task->room->plant . ')' : '-',
                    'task_pic' => $taskPic,
                    'task_start_date' => $task->start_date,
                    'task_due_date' => $task->due_date,
                    'task_status' => $task->status,
                    'items' => [],
                ];
            }
            
            // Determine PIC (Person In Charge) for item
            $pic = 'Tidak ada';
            if ($item->assignedUser) {
                $pic = $item->assignedUser->name;
            } elseif ($task->delegations->isNotEmpty()) {
                $delegation = $task->delegations->first();
                $pic = $delegation->delegatedTo->name;
            }
            
            // Count photos from all updates
            $photoCount = 0;
            if ($item->updates) {
                foreach ($item->updates as $update) {
                    if ($update->attachments && is_array($update->attachments)) {
                        $photoCount += count($update->attachments);
                    }
                }
            }
            
            // Determine date range for this item (use task item's start_date and due_date first)
            $itemStartDate = $item->start_date
                ? Carbon::parse($item->start_date)
                : ($item->due_date
                    ? Carbon::parse($item->due_date)->copy()->subDays(7)
                    : ($task->start_date ? Carbon::parse($task->start_date) : Carbon::today()));
            $itemEndDate = $item->due_date
                ? Carbon::parse($item->due_date)
                : ($item->start_date
                    ? Carbon::parse($item->start_date)->copy()->addDays(7)
                    : ($task->due_date ? Carbon::parse($task->due_date) : $itemStartDate->copy()->addDays(7)));
            
            $groupedData[$taskId]['items'][] = [
                'id' => $item->id,
                'item_title' => $item->title,
                'item_description' => $item->description,
                'pic' => $pic,
                'status' => $item->status,
                'progress' => $item->progress_percentage,
                'photo_count' => $photoCount,
                'start_date' => $itemStartDate,
                'end_date' => $itemEndDate,
                'due_date' => $item->due_date,
            ];
        }
        
        // Convert to array for easier handling in view
        $reportData = array_values($groupedData);
        
        // Get rooms for filter
        $rooms = \App\Models\Room::orderBy('room')->get();
        
        // Get all users for filter dropdown (subordinates + self for regular users, all for superuser)
        $filterUsers = $isSuperuser 
            ? \App\Models\User::with('position')->orderBy('name')->get()
            : $subordinates;
        
        return view('reports.timeline', compact('reportData', 'dateRange', 'rooms', 'roomId', 'status', 'dateFrom', 'dateTo', 'groupBy', 'isSuperuser', 'filterUsers', 'filterUserId'));
    }

    /**
     * Print report for specific task
     */
    public function printTask(Request $request, Task $task)
    {
        $user = Auth::user();
        $isSuperuser = $user->position && $user->position->name === 'Superuser';
        
        // Check authorization
        if (!$isSuperuser) {
            $subordinates = $user->getSubordinatesIncludingSelf();
            $allowedUserIds = $subordinates->pluck('id')->toArray();
            
            // Check if user can see this task
            $canSee = $task->created_by == $user->id || 
                     $task->delegations()->whereIn('delegated_to', $allowedUserIds)->exists() ||
                     in_array($task->created_by, $allowedUserIds);
            
            if (!$canSee) {
                abort(403, 'Anda tidak memiliki akses untuk melihat laporan ini.');
            }
        }
        
        // Load task with relations
        $task->load([
            'room',
            'creator.position',
            'taskItems.assignedUser.position',
            'taskItems.updates',
            'delegations.delegatedTo.position'
        ]);
        
        // Calculate date range for timeline
        $minDate = null;
        $maxDate = null;
        
        foreach ($task->taskItems as $item) {
            // Use task item's start_date and due_date first, then fallback to task dates
            $startDate = $item->start_date 
                ?: ($item->due_date 
                    ? $item->due_date->copy()->subDays(7) 
                    : ($task->start_date ?: Carbon::today()));
            $endDate = $item->due_date 
                ?: ($item->start_date 
                    ? $item->start_date->copy()->addDays(7) 
                    : ($task->due_date ?: $startDate->copy()->addDays(7)));
            
            if (!$minDate || $startDate < $minDate) {
                $minDate = $startDate->copy();
            }
            if (!$maxDate || $endDate > $maxDate) {
                $maxDate = $endDate->copy();
            }
        }
        
        // If no data, use default range
        if (!$minDate || !$maxDate) {
            $minDate = Carbon::today()->subDays(30);
            $maxDate = Carbon::today()->addDays(30);
        } else {
            // Extend range a bit before and after
            $minDate->subDays(7);
            $maxDate->addDays(7);
        }
        
        // Generate date range array
        $dateRange = [];
        $current = $minDate->copy();
        while ($current <= $maxDate) {
            $dateRange[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        // Limit date range to prevent too many columns (max 90 days visible)
        if (count($dateRange) > 90) {
            $today = Carbon::today();
            $start = $today->copy()->subDays(45);
            $end = $today->copy()->addDays(45);
            
            $dateRange = [];
            $current = $start->copy();
            while ($current <= $end) {
                $dateRange[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }
        
        return view('reports.print-task', compact('task', 'dateRange'));
    }
}

