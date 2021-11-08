<?php

namespace Database\Seeders;

use App\Models\Visitation;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class visitationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i<1000; $i++){
            $faker = Faker::create();
            Visitation::create([
                'place_id' => 1,
                'user_id' => 2,
                'visitor' => rand(1, 20),
                'user_visits_id' => rand(1, 20),
                'date' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now'),
            ]);
        }

    }
}
