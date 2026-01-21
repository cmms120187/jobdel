<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubordinateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show recursive subordinates for current leader
     */
    public function index()
    {
        $user = Auth::user();

        // Only allow users who have subordinates or are not Superuser
        if ($user->position && $user->position->name === 'Superuser') {
            // Superuser can see all users; redirect to admin users
            return redirect()->route('admin.users.index');
        }

        // Fetch recursive subordinates using model helper
        $subordinates = $user->getSubordinatesIncludingSelf();

        return view('leader.subordinates.index', compact('subordinates'));
    }

    /**
     * Show profile of a subordinate (reuse admin show view if exists)
     */
    public function show($id)
    {
        $user = Auth::user();
        $subordinates = $user->getSubordinatesIncludingSelf();

        $target = $subordinates->firstWhere('id', $id);
        if (!$target) {
            abort(403);
        }

        // If admin users.show blade exists, reuse it; otherwise show simple profile
        if (view()->exists('admin.users.show')) {
            return view('admin.users.show', ['user' => $target]);
        }

        return view('leader.subordinates.show', ['user' => $target]);
    }
}
