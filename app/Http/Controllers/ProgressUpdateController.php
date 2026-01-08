<?php

namespace App\Http\Controllers;

use App\Models\Delegation;
use App\Models\ProgressUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressUpdateController extends Controller
{
    public function store(Request $request, Delegation $delegation)
    {
        if ($delegation->delegated_to !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB per photo
        ]);

        // Handle photo uploads
        $attachments = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $attachments[] = $photo->store('progress-photos', 'public');
            }
        }

        $progressUpdate = ProgressUpdate::create([
            'delegation_id' => $delegation->id,
            'updated_by' => Auth::id(),
            'progress_percentage' => $validated['progress_percentage'],
            'notes' => $validated['notes'] ?? null,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        // Update delegation progress
        $delegation->update([
            'progress_percentage' => $validated['progress_percentage'],
            'status' => $validated['progress_percentage'] == 100 ? 'completed' : 'in_progress',
        ]);

        // Update task status if progress is 100%
        if ($validated['progress_percentage'] == 100) {
            $delegation->task->update(['status' => 'completed']);
            $delegation->update(['completed_at' => now()]);
        } elseif ($delegation->task->status == 'pending') {
            $delegation->task->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success', 'Progress berhasil diperbarui.');
    }

    public function destroy(ProgressUpdate $progressUpdate)
    {
        $delegation = $progressUpdate->delegation;
        $progressUpdate->delete();
        
        return redirect()->back()->with('success', 'Update progress berhasil dihapus.');
    }
}
