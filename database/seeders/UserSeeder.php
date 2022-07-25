<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = "12345";
        for($i = 0;$i < 5;$i++) {
            $email = "admin" . ($i > 0 ? $i : "") . "@gmail.com";
            $name = "Admin " . ($i + 1); 
            $params = [
                "name" => $name,
                "email" => $email,
                "password" => bcrypt($password)
            ];
            $existing = DB::table("users")->whereName($name)->count("id");
            
            if($existing > 0) continue;

            DB::table("users")->insert($params);

        }
    }
}
