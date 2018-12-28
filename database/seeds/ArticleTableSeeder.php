<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\Article;
use Carbon\Carbon;

class ArticleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $categories = Category::pluck('id');
        $datas = [];

        foreach (range(1, 1000) as $index) {
            $datas[] = [
                'title'        => $faker->sentence(),
                'category_id'  => $faker->randomElement($categories),
                'slug'         => $faker->slug(),
                'content'      => $faker->text(),
                'status'       => $faker->boolean,
                'user_id'      => 1,
                'published_at' => Carbon::now()->toDateTimeString(),
                'created_at'   => Carbon::now()->toDateTimeString(),
                'updated_at'   => Carbon::now()->toDateTimeString()
            ];
        }
        DB::table('articles')->insert($datas);
    }
}
