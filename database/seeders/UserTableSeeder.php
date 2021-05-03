<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $username = ['user demo', 'admin demo', 'real admin'];
      $email = ['userdemo@gmail.com', 'admindemo@gmail.com', 'admin@gmail.com'];
      $role =['agent', 'agent', 'admin'];
      $password = 'userdemo';

      for ($i=0; $i < 3; $i++) {
        $token = Crypt::encrypt($email[$i].'+'. $password);
          User::create([
            'username' => $username[$i],
            'email' => $email[$i],
            'password' => Hash::make($password),
            'api_token' => $token,
            'role' => $role[$i]
          ]);
      }

    }
}
