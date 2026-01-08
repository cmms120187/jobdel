<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Staff',
                'level' => 1,
                'description' => 'Staff level - Entry level position',
            ],
            [
                'name' => 'Leader',
                'level' => 2,
                'description' => 'Leader - Team leader position',
            ],
            [
                'name' => 'Supervisor',
                'level' => 3,
                'description' => 'Supervisor - Supervisory position',
            ],
            [
                'name' => 'Ast Manager',
                'level' => 4,
                'description' => 'Assistant Manager - Assistant management position',
            ],
            [
                'name' => 'Manager',
                'level' => 5,
                'description' => 'Manager - Management position',
            ],
            [
                'name' => 'GM',
                'level' => 6,
                'description' => 'General Manager - Top management position',
            ],
            [
                'name' => 'Superuser',
                'level' => 7,
                'description' => 'Superuser - Full system access',
            ],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
