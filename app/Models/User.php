<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'name',
        'email',
        'password',
        'position_id',
        'leader_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function delegations()
    {
        return $this->hasMany(Delegation::class, 'delegated_to');
    }

    public function delegatedTasks()
    {
        return $this->hasMany(Delegation::class, 'delegated_by');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class, 'updated_by');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'leader_id');
    }

    /**
     * Generate email from first name
     */
    public static function generateEmailFromName($name)
    {
        $firstName = strtolower(explode(' ', trim($name))[0]);
        // Remove special characters and spaces
        $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
        return $firstName . '@pai.pratama.net';
    }

    /**
     * Get users that can be delegated to (only subordinates + self)
     * Based on position hierarchy - can only delegate to lower level positions
     * User can also delegate to themselves
     */
    public function getDelegatableUsers()
    {
        // Superuser can delegate to everyone including themselves
        if ($this->position && $this->position->name === 'Superuser') {
            return User::with('position')
                ->orderBy('name')
                ->get();
        }

        // Prefer leader/subordinate relation: if this user has direct subordinates
        // (users with leader_id = this user's id), return them plus self.
        $directSubs = $this->subordinates()->with('position')->orderBy('name')->get();
        if ($directSubs->isNotEmpty()) {
            return $directSubs->push($this->load('position'))->unique('id')->sortBy('name')->values();
        }

        // If user has no position, only return self
        if (!$this->position) {
            return collect([$this->load('position')]);
        }

        $userLevel = $this->position->level;

        // Fallback: Get users with position level lower than or equal to current user (includes self)
        return User::whereHas('position', function($query) use ($userLevel) {
                $query->where('level', '<=', $userLevel);
            })
            ->with('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get users that are superiors (higher level positions)
     * Based on position hierarchy - users with level higher than current user
     * Returns all users with higher position levels (not just direct leader)
     */
    public function getSuperiors()
    {
        // Superuser has no superiors
        if ($this->position && $this->position->name === 'Superuser') {
            return collect([]);
        }

        // If user has no position, return empty collection
        if (!$this->position) {
            return collect([]);
        }

        $userLevel = $this->position->level;

        // Get all users with position level higher than current user
        // This includes all levels above (e.g., if Ast Manager, shows Manager, GM, etc.)
        return User::whereHas('position', function($query) use ($userLevel) {
                $query->where('level', '>', $userLevel);
            })
            ->with('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all subordinates (users with lower position level)
     * Based on position hierarchy - users with level lower than current user
     * Includes direct subordinates and all their subordinates recursively
     */
    public function getSubordinates()
    {
        // Superuser can see all users
        if ($this->position && $this->position->name === 'Superuser') {
            return User::with('position')
                ->orderBy('name')
                ->get();
        }

        // If user has direct subordinates via leader_id, prefer that set
        $directSubs = $this->subordinates()->with('position')->orderBy('name')->get();
        if ($directSubs->isNotEmpty()) {
            // Collect recursively (breadth-first) to include subordinates of subordinates
            $all = collect();
            $queue = $directSubs->values();

            while ($queue->isNotEmpty()) {
                $current = $queue->shift();
                $all->push($current->load('position'));

                $children = $current->subordinates()->with('position')->orderBy('name')->get();
                if ($children->isNotEmpty()) {
                    foreach ($children as $child) {
                        $queue->push($child);
                    }
                }
            }

            return $all->unique('id')->sortBy('name')->values();
        }

        // If user has no position, return empty collection
        if (!$this->position) {
            return collect([]);
        }

        $userLevel = $this->position->level;

        // Fallback: Get users with position level lower than current user
        return User::whereHas('position', function($query) use ($userLevel) {
                $query->where('level', '<', $userLevel);
            })
            ->with('position')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get subordinates including self
     */
    public function getSubordinatesIncludingSelf()
    {
    $subordinates = $this->getSubordinates();
    return $subordinates->push($this->load('position'))->unique('id')->sortBy('name')->values();
    }
}
