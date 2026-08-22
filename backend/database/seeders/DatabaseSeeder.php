<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cinema;
use App\Models\Auditorium;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $starlight = Cinema::firstOrCreate(
            ['name' => 'Starlight Multiplex'],
            ['location' => 'Downtown Mall, Level 3']
        );

        Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'Hall A'],
            ['rows_count' => 12, 'seats_per_row' => 10, 'total_seats' => 120]
        );

        Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'Hall B'],
            ['rows_count' => 8, 'seats_per_row' => 10, 'total_seats' => 80]
        );

        Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'IMAX Hall'],
            ['rows_count' => 10, 'seats_per_row' => 20, 'total_seats' => 200]
        );

        $vip = Cinema::firstOrCreate(
            ['name' => 'VIP Cinema Center'],
            ['location' => 'Metropolitan Plaza']
        );

        Auditorium::firstOrCreate(
            ['cinema_id' => $vip->id, 'name' => 'VIP Lounge'],
            ['rows_count' => 4, 'seats_per_row' => 8, 'total_seats' => 32]
        );

        Auditorium::firstOrCreate(
            ['cinema_id' => $vip->id, 'name' => 'Screen 1'],
            ['rows_count' => 6, 'seats_per_row' => 10, 'total_seats' => 60]
        );
    }
}
