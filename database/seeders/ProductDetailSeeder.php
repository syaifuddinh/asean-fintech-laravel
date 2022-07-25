<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use DB;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $categories = DB::table("product")->get();
        for($i = 0;$i < 700;$i++) {
            $parent = $faker->randomElement($categories);
            $name = $parent->name . " - " . $faker->word;
            $productId = $parent->id;
            $params = [
                "name" => $name,
                "productId" => $productId,
                "productionDate" => $faker->dateTimeThisMonth(),
                "price" => $faker->randomNumber(2) * 10000,
                "created_at" => date("Y-m-d H:i:s")
            ];
            $existing = DB::table("product_detail")->whereName($name)->count("id");
            
            if($existing > 0) continue;

            DB::table("product_detail")->insert($params);

        }
    }
}
