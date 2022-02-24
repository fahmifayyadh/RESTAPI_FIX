<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $name = ['plengkung', 'Teluk hijau', 'pulau merah', 'tabuhan', 'pantai boom', 'taman nasional baluran'];
      $location = ['muncar - banyuwangi', 'muncar - banyuwangi', 'muncar - banyuwangi', 'Tabuhan- banyuwangi', 'Banyuwangi kota - banyuwangi', 'Banyuwangi kota - banyuwangi'];

      for ($i=0; $i < 6; $i++) {
        Place::create([
            'name' => $name[$i],
            'fee_local' => 50000,
            'fee_inter' => 100000,
            'location' => $location[$i],
        ]);
      }

    }
}
