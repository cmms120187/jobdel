<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            [
                'room' => 'IR',
                'plant' => 'Plant A',
                'description' => 'Industrial Room - Plant A',
            ],
            [
                'room' => 'Production Room 1',
                'plant' => 'Plant A',
                'description' => 'Production Room 1 di Plant A',
            ],
            [
                'room' => 'Production Room 2',
                'plant' => 'Plant A',
                'description' => 'Production Room 2 di Plant A',
            ],
            [
                'room' => 'Warehouse',
                'plant' => 'Plant B',
                'description' => 'Gudang penyimpanan di Plant B',
            ],
            [
                'room' => 'Quality Control',
                'plant' => 'Plant B',
                'description' => 'Ruangan Quality Control',
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
