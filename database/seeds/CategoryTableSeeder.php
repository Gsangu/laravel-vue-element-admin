<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([[
            'name'       => 'default',
            'slug'       => 'default',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ],
        [
            'name'       => 'news',
            'slug'       => 'news',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ],
        [
            'name'       => 'products',
            'slug'       => 'products',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ],
        [
            'name'       => 'cases',
            'slug'       => 'cases',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ],
        [
            'name'       => 'solutions',
            'slug'       => 'solutions',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]
        ]);
        //$categories = factory(App\Category::class)->times(10)->make();
        //App\Category::insert($categories->toArray());
    }
}
