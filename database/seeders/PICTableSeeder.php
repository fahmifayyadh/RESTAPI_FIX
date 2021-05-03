<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pic;

class PICTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      PIC::create([
          'user_id' => 2,
          'place_id' => 1
      ]);
    }
}
