<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Bengkel Otomotif',
                'person_in_charge' => 'Bpk. Budi Santoso'
            ],
            [
                'name' => 'Lab Komputer RPL',
                'person_in_charge' => 'Ibu Siti Aminah'
            ],
            [
                'name' => 'Gudang Sarpras Utama',
                'person_in_charge' => 'Bpk. Ahmad Ridwan'
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
