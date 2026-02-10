<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoController extends Controller
{
    /**
     * STO (Struktur Organisasi) berdasarkan hirarki di tabel users (leader_id).
     * Administrator (Superuser): melihat hirarki semua user.
     * User lain: hanya melihat data diri dan bawahan (diri + bawahan rekursif).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->position && $user->position->name === 'Superuser') {
            $tree = $this->buildFullHierarchyTree();
        } else {
            $tree = $this->buildUserHierarchyTree($user);
        }

        return view('sto.index', compact('tree'));
    }

    /**
     * Build full org tree: roots = users with no leader, then recursive children.
     * Hanya untuk Administrator (Superuser).
     */
    private function buildFullHierarchyTree(): array
    {
        $roots = User::whereNull('leader_id')
            ->with('position')
            ->orderBy('name')
            ->get();

        $allUsers = User::with('position')->get()->keyBy('id');

        $tree = [];
        /** @var User $root */
        foreach ($roots as $root) {
            $tree[] = [
                'user' => $root,
                'children' => $this->buildChildrenNodes($root, $allUsers),
            ];
        }

        return $tree;
    }

    /**
     * Build children nodes for a user (recursive) — untuk full hierarchy.
     *
     * @param  \Illuminate\Support\Collection<int, User>  $allUsers
     */
    private function buildChildrenNodes(User $user, $allUsers): array
    {
        $children = $allUsers->where('leader_id', $user->id)->sortBy('name')->values();
        $nodes = [];
        foreach ($children as $child) {
            if (!$child instanceof User) {
                continue;
            }
            $nodes[] = [
                'user' => $child,
                'children' => $this->buildChildrenNodes($child, $allUsers),
            ];
        }
        return $nodes;
    }

    /**
     * Build tree starting from current user (self as root, subordinates recursively).
     */
    private function buildUserHierarchyTree(User $user): array
    {
        $user->load('position');
        $directSubs = $user->subordinates()->with('position')->orderBy('name')->get();

        $children = [];
        foreach ($directSubs as $sub) {
            $children[] = [
                'user' => $sub,
                'children' => $this->buildUserChildrenNodes($sub),
            ];
        }

        return [
            [
                'user' => $user,
                'children' => $children,
            ],
        ];
    }

    /**
     * Build children nodes for a user in "my hierarchy" tree (recursive).
     */
    private function buildUserChildrenNodes(User $user): array
    {
        $children = $user->subordinates()->with('position')->orderBy('name')->get();
        $nodes = [];
        foreach ($children as $child) {
            $nodes[] = [
                'user' => $child,
                'children' => $this->buildUserChildrenNodes($child),
            ];
        }
        return $nodes;
    }
}
