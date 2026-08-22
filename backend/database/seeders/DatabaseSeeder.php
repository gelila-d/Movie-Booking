<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cinema;
use App\Models\Auditorium;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with Birr pricing starting at 100 Birr.
     */
    public function run(): void
    {
        $starlight = Cinema::firstOrCreate(
            ['name' => 'Starlight Multiplex'],
            ['location' => 'Downtown Mall, Level 3']
        );

        $hallA = Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'Hall A'],
            ['rows_count' => 10, 'seats_per_row' => 12, 'vip_rows_count' => 2, 'base_price' => 100.00, 'total_seats' => 120]
        );

        $hallB = Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'Hall B'],
            ['rows_count' => 8, 'seats_per_row' => 10, 'vip_rows_count' => 2, 'base_price' => 100.00, 'total_seats' => 80]
        );

        $imax = Auditorium::firstOrCreate(
            ['cinema_id' => $starlight->id, 'name' => 'IMAX Hall'],
            ['rows_count' => 10, 'seats_per_row' => 20, 'vip_rows_count' => 3, 'base_price' => 150.00, 'total_seats' => 200]
        );

        $vip = Cinema::firstOrCreate(
            ['name' => 'VIP Cinema Center'],
            ['location' => 'Metropolitan Plaza']
        );

        $vipLounge = Auditorium::firstOrCreate(
            ['cinema_id' => $vip->id, 'name' => 'VIP Lounge'],
            ['rows_count' => 4, 'seats_per_row' => 8, 'vip_rows_count' => 4, 'base_price' => 200.00, 'total_seats' => 32]
        );

        $screen1 = Auditorium::firstOrCreate(
            ['cinema_id' => $vip->id, 'name' => 'Screen 1'],
            ['rows_count' => 6, 'seats_per_row' => 10, 'vip_rows_count' => 2, 'base_price' => 120.00, 'total_seats' => 60]
        );

        // Seed default showtimes with Birr pricing if movies exist
        $movies = Movie::all();
        if ($movies->count() > 0) {
            $movie = $movies->first();
            
            Showtime::firstOrCreate(
                ['movie_id' => $movie->id, 'auditorium_id' => $hallA->id],
                [
                    'auditorium' => 'Starlight Multiplex - Hall A',
                    'start_time' => Carbon::now()->addDays(1)->setTime(14, 0),
                    'end_time' => Carbon::now()->addDays(1)->setTime(16, 30),
                    'price' => 100.00,
                    'vip_price' => 150.00,
                    'student_price' => 80.00,
                    'child_price' => 60.00,
                    'total_seats' => 120,
                    'available_seats' => 120
                ]
            );

            Showtime::firstOrCreate(
                ['movie_id' => $movie->id, 'auditorium_id' => $imax->id],
                [
                    'auditorium' => 'Starlight Multiplex - IMAX Hall',
                    'start_time' => Carbon::now()->addDays(1)->setTime(18, 0),
                    'end_time' => Carbon::now()->addDays(1)->setTime(20, 30),
                    'price' => 150.00,
                    'vip_price' => 220.00,
                    'student_price' => 120.00,
                    'child_price' => 90.00,
                    'total_seats' => 200,
                    'available_seats' => 200
                ]
            );
        }
    }
}
