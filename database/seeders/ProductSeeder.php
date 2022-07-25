<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $categories = DB::table("product_category")->get();
        for($i = 0;$i < 400;$i++) {
            $parent = $faker->randomElement($categories);
            $name = $parent->name . " - " . $faker->word;
            $productCategoryId = $parent->id;
            $params = [
                "name" => $name,
                "productCategoryId" => $productCategoryId,
                "created_at" => date("Y-m-d H:i:s")
            ];
            $existing = DB::table("product")->whereName($name)->count("id");
            
            if($existing > 0) continue;

            DB::table("product")->insert($params);

        }
    }
}
