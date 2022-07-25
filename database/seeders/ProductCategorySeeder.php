<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        for($i = 0;$i < 200;$i++) {
            $name = $faker->word;
            $params = [
                "name" => $name,
                "created_at" => date("Y-m-d H:i:s")
            ];
            $existing = DB::table("product_category")->whereName($name)->count("id");
            
            if($existing > 0) continue;

            DB::table("product_category")->insert($params);

        }
    }
}
