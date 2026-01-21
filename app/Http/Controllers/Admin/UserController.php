<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->position) {
                abort(403, 'Unauthorized access.');
            }

            // Superuser can access everything
            if ($user->position->name === 'Superuser') {
                return $next($request);
            }

            // Leaders (position level > 1) are allowed to access admin users
            // but will be limited to viewing their own subordinates in the index.
            if ($user->position->level > 1) {
                return $next($request);
            }

            abort(403, 'Unauthorized access.');
        });
    }

    /**
     * Only Superuser may modify users (create/store/edit/update/destroy).
     */
    protected function authorizeSuperuser()
    {
        $user = Auth::user();
        if (!$user || !$user->position || $user->position->name !== 'Superuser') {
            abort(403, 'Only Superuser may perform this action.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get page from request, session, or default to 1
        $page = $request->get('page', session('admin_users_page', 1));
        
        // Save current page to session
        session(['admin_users_page' => $page]);
        
        $currentUser = Auth::user();

        if ($currentUser->position && $currentUser->position->name === 'Superuser') {
            $usersQuery = User::with(['position', 'leader.position'])->orderBy('name');
        } else {
            // Leaders can only see their direct subordinates and themselves
            $usersQuery = User::with(['position', 'leader.position'])
                ->where(function($q) use ($currentUser) {
                    $q->where('leader_id', $currentUser->id)
                      ->orWhere('id', $currentUser->id);
                })
                ->orderBy('name');
        }

        $users = $usersQuery->paginate(15, ['*'], 'page', $page);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $this->authorizeSuperuser();
        $positions = Position::orderBy('level')->get();
        $leaders = User::whereHas('position', function($query) {
            $query->where('level', '>', 1); // Exclude Staff level
        })->with('position')->orderBy('name')->get();
        
        return view('admin.users.create', compact('positions', 'leaders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $this->authorizeSuperuser();
        $validated = $request->validate([
            'nik' => 'required|string|min:5|max:255|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:5',
            'position_id' => 'required|exists:positions,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        // Generate email from name if not provided
        if (empty($validated['email'])) {
            $validated['email'] = User::generateEmailFromName($validated['name']);
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        // Get page from session for redirect
        $page = session('admin_users_page', 1);
        
        return redirect()->route('admin.users.index', ['page' => $page])
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['position', 'leader.position', 'subordinates.position']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
    $this->authorizeSuperuser();
        $positions = Position::orderBy('level')->get();
        $leaders = User::where('id', '!=', $user->id)
            ->whereHas('position', function($query) use ($user) {
                // Leader harus memiliki level lebih tinggi dari user
                if ($user->position) {
                    $query->where('level', '>', $user->position->level);
                } else {
                    $query->where('level', '>', 1);
                }
            })
            ->with('position')
            ->orderBy('name')
            ->get();
        
        return view('admin.users.edit', compact('user', 'positions', 'leaders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
    $this->authorizeSuperuser();
        $validated = $request->validate([
            'nik' => 'required|string|min:5|max:255|unique:users,nik,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:5',
            'position_id' => 'required|exists:positions,id',
            'leader_id' => 'nullable|exists:users,id',
        ]);

        // Don't update password if not provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Prevent user from being their own leader
        if ($validated['leader_id'] == $user->id) {
            return redirect()->back()
                ->withErrors(['leader_id' => 'User tidak bisa menjadi leader untuk dirinya sendiri.'])
                ->withInput();
        }

        $user->update($validated);

        // Get page from session for redirect
        $page = session('admin_users_page', 1);
        
        return redirect()->route('admin.users.index', ['page' => $page])
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
    $this->authorizeSuperuser();
        // Prevent deleting superuser
        if ($user->position && $user->position->name === 'Superuser') {
            return redirect()->back()
                ->withErrors(['error' => 'Superuser tidak bisa dihapus.']);
        }

        $user->delete();

        // Get page from session for redirect
        $page = session('admin_users_page', 1);
        
        return redirect()->route('admin.users.index', ['page' => $page])
            ->with('success', 'User berhasil dihapus.');
    }
}
