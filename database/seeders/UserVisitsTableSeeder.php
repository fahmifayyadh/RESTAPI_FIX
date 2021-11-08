<?php

namespace Database\Seeders;

use App\Models\UserVisit;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserVisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i=0; $i<20; $i++){
            UserVisit::create([
                'name' => $faker->name,
                'identity'=> rand(1, 4),
                'identity_number' => $faker->creditCardNumber,
                'province' => 'Jawa Timur',
                'district' => 'Banyuwangi',
                'overseas' => 0
            ]);
        }

    }
}
