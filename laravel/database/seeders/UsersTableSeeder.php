<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->insert([
        [
          'id' => 1,
          'name' => 'DIN DIGITAL',
          'email' => 'suporte@dindigital.com',
          'password' => bcrypt('din123456'),
          'created_at' => date('Y-m-d H:i:s'),
        ]
      ]);
    }
}
