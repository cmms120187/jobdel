@php
    use Illuminate\Support\Facades\Storage;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - {{ $task->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/iconjobdel.png') }}">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            /* Ensure colors print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #6366f1;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 8px;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
            color: white;
        }
        .header h2 {
            margin: 10px 0 0 0;
            font-size: 22pt;
            font-weight: bold;
            color: white;
        }
        .task-info {
            margin-bottom: 15px;
            padding: 12px;
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
            border-radius: 8px;
            border-left: 4px solid #6366f1;
        }
        .task-info p {
            margin: 4px 0;
            font-size: 9pt;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
            font-size: 8pt;
        }
        th {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: bold;
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        tbody tr:hover {
            background-color: #f1f5f9;
        }
        .timeline-cell {
            width: 25px;
            min-width: 25px;
            text-align: center;
            padding: 3px;
            border: 1px solid #e2e8f0;
        }
        .timeline-filled-pending {
            background-color: #fef08a !important;
            color: #713f12;
        }
        .timeline-filled-in_progress {
            background-color: #93c5fd !important;
            color: #1e3a8a;
        }
        .timeline-filled-completed {
            background-color: #86efac !important;
            color: #14532d;
        }
        .timeline-filled-cancelled {
            background-color: #fca5a5 !important;
            color: #7f1d1d;
        }
        .timeline-today {
            background-color: #fef3c7 !important;
            border: 2px solid #f59e0b;
        }
        .timeline-weekend {
            background-color: #f1f5f9 !important;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        .status-pending { 
            background-color: #fef08a !important; 
            color: #713f12 !important;
        }
        .status-in_progress { 
            background-color: #93c5fd !important; 
            color: #1e3a8a !important;
        }
        .status-completed { 
            background-color: #86efac !important; 
            color: #14532d !important;
        }
        .status-cancelled { 
            background-color: #fca5a5 !important; 
            color: #7f1d1d !important;
        }
        .progress-bar {
            width: 80px;
            height: 10px;
            background-color: #e2e8f0;
            border-radius: 5px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #6366f1 100%);
            border-radius: 5px;
            transition: width 0.3s ease;
        }
        .photo-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #22c55e;
            color: white;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
        }
        h3 {
            margin: 0;
            font-size: 14pt;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12pt;
            z-index: 1000;
        }
        .print-button:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print</button>

    <div class="header">
        <h1>LAPORAN PROJECT MANAGEMENT</h1>
        <h2>{{ $task->title }}</h2>
    </div>

    <div class="task-info">
        <p><strong>Project Code:</strong> {{ $task->project_code ?: '-' }}</p>
        <p><strong>Room:</strong> {{ $task->room ? $task->room->room . ' (' . $task->room->plant . ')' : '-' }}</p>
        <p><strong>PIC Project:</strong> 
            @if($task->delegations->isNotEmpty())
                {{ $task->delegations->first()->delegatedTo->name }}
            @elseif($task->creator)
                {{ $task->creator->name }}
            @else
                -
            @endif
        </p>
        <p><strong>Periode:</strong> 
            @if($task->start_date)
                {{ $task->start_date->format('d M Y') }}
            @else
                -
            @endif
            s/d 
            @if($task->due_date)
                {{ $task->due_date->format('d M Y') }}
            @else
                -
            @endif
        </p>
        @if($task->description)
            <p><strong>Deskripsi:</strong> {{ $task->description }}</p>
        @endif
        <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>

    @if($task->taskItems->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 200px;">Detail Pekerjaan</th>
                    <th style="width: 250px;">Deskripsi Pekerjaan</th>
                    <th style="width: 150px;">PIC</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 80px;">Progress</th>
                    @foreach($dateRange as $date)
                        <th class="timeline-cell">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($task->taskItems as $item)
                    @php
                        // Determine PIC
                        $pic = 'Tidak ada';
                        if ($item->assignedUser) {
                            $pic = $item->assignedUser->name;
                        } elseif ($task->delegations->isNotEmpty()) {
                            $delegation = $task->delegations->first();
                            $pic = $delegation->delegatedTo->name;
                        }

                        // Determine date range (use task item's start_date and due_date first)
                        $itemStartDate = $item->start_date 
                            ?: ($item->due_date 
                                ? $item->due_date->copy()->subDays(7) 
                                : ($task->start_date ?: \Carbon\Carbon::today()));
                        $itemEndDate = $item->due_date 
                            ?: ($item->start_date 
                                ? $item->start_date->copy()->addDays(7) 
                                : ($task->due_date ?: $itemStartDate->copy()->addDays(7)));
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->title }}</strong></td>
                        <td>{{ $item->description ?: '-' }}</td>
                        <td>{{ $pic }}</td>
                        <td>
                            <span class="status-badge status-{{ $item->status }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $item->progress_percentage }}%;"></div>
                            </div>
                            <strong>{{ $item->progress_percentage }}%</strong>
                        </td>
                        @foreach($dateRange as $date)
                            @php
                                $dateObj = \Carbon\Carbon::parse($date);
                                $itemStart = $itemStartDate->copy()->startOfDay();
                                $itemEnd = $itemEndDate->copy()->endOfDay();
                                $isInRange = $dateObj->greaterThanOrEqualTo($itemStart) && $dateObj->lessThanOrEqualTo($itemEnd);
                                $isToday = $dateObj->isToday();
                                $isWeekend = $dateObj->isWeekend();
                                
                                // Determine timeline cell class based on status and range
                                $timelineClass = 'timeline-cell';
                                if ($isInRange) {
                                    $timelineClass .= ' timeline-filled-' . $item->status;
                                } elseif ($isToday) {
                                    $timelineClass .= ' timeline-today';
                                } elseif ($isWeekend) {
                                    $timelineClass .= ' timeline-weekend';
                                }
                            @endphp
                            <td class="{{ $timelineClass }}">
                                @if($isInRange)
                                    ●
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada detail pekerjaan untuk task ini.</p>
    @endif

    @php
        // Check if there are any photos across all task items
        $hasAnyPhotos = false;
        $itemsWithPhotos = collect();
        
        if ($task->taskItems->count() > 0) {
            foreach ($task->taskItems as $item) {
                $allPhotos = collect();
                if ($item->updates) {
                    foreach ($item->updates as $update) {
                        if ($update->attachments && is_array($update->attachments)) {
                            foreach ($update->attachments as $photo) {
                                $allPhotos->push([
                                    'photo' => $photo,
                                    'update_date' => $update->update_date ? \Carbon\Carbon::parse($update->update_date)->format('d M Y') : $update->created_at->format('d M Y'),
                                    'notes' => $update->notes,
                                    'updater' => $update->updater->name ?? 'Unknown',
                                ]);
                            }
                        }
                    }
                }
                
                if ($allPhotos->count() > 0) {
                    $hasAnyPhotos = true;
                    $itemsWithPhotos->push([
                        'item' => $item,
                        'photos' => $allPhotos,
                    ]);
                }
            }
        }
    @endphp

    <div style="margin-top: 30px; text-align: center; font-size: 9pt; color: #666;">
        <p>Dicetak dari sistem Job Delegation pada {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
        @if($hasAnyPhotos)
            <p style="margin-top: 10px; font-weight: bold; color: #6366f1; font-size: 10pt;">📎 Dokumentasi foto kegiatan tersedia di halaman berikutnya</p>
        @endif
    </div>

    @if($hasAnyPhotos)
        <!-- Page Break for Documentation -->
        <div style="page-break-before: always;"></div>

        <!-- Documentation Page -->
        <div class="header">
            <h1>LAPORAN PROJECT MANAGEMENT</h1>
            <h2>Dokumentasi Foto Kegiatan - {{ $task->title }}</h2>
        </div>

        <div class="task-info">
            <p><strong>Project Code:</strong> {{ $task->project_code ?: '-' }}</p>
            <p><strong>Room:</strong> {{ $task->room ? $task->room->room . ' (' . $task->room->plant . ')' : '-' }}</p>
            <p><strong>PIC Project:</strong> 
                @if($task->delegations->isNotEmpty())
                    {{ $task->delegations->first()->delegatedTo->name }}
                @elseif($task->creator)
                    {{ $task->creator->name }}
                @else
                    -
                @endif
            </p>
            <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
        </div>

        @foreach($itemsWithPhotos as $itemData)
            <div style="page-break-inside: avoid; margin-bottom: 30px; padding: 15px; background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%); border-radius: 8px; border-left: 4px solid #6366f1;">
                <h3 style="margin: 0 0 10px 0; font-size: 14pt; color: #1e293b; font-weight: bold;">
                    Detail Pekerjaan: {{ $itemData['item']->title }}
                </h3>
                <p style="margin: 0 0 15px 0; font-size: 9pt; color: #475569;">
                    Total Foto: <strong style="color: #6366f1;">{{ $itemData['photos']->count() }}</strong>
                    @if($itemData['item']->description)
                        | {{ $itemData['item']->description }}
                    @endif
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                    @foreach($itemData['photos'] as $photoData)
                        <div style="page-break-inside: avoid; border: 2px solid #e2e8f0; border-radius: 6px; padding: 8px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <img src="{{ Storage::url($photoData['photo']) }}" alt="Foto Dokumentasi" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; margin-bottom: 8px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 7pt; color: #475569;">
                                <p style="margin: 2px 0; font-weight: bold; color: #6366f1;">📅 {{ $photoData['update_date'] }}</p>
                                <p style="margin: 2px 0;">👤 Oleh: {{ $photoData['updater'] }}</p>
                                @if($photoData['notes'])
                                    <p style="margin: 4px 0 0 0; font-style: italic; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 4px;">{{ Str::limit($photoData['notes'], 60) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div style="margin-top: 30px; text-align: center; font-size: 9pt; color: #666;">
            <p>Dicetak dari sistem Job Delegation pada {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
        </div>
    @endif
</body>
</html>

