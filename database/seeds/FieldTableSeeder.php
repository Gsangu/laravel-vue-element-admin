<?php

use Illuminate\Database\Seeder;

class FieldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fields')->insert([
            [
                'name'    => '产品',
                'index'   => 'products'
            ],
            [
                'name'    => '内容',
                'index'   => 'content'
            ],
            [
                'name'    => '电话',
                'index'   => 'tel'
            ],
            [
                'name'    => '国家',
                'index'   => 'country'
            ],
            [
                'name'    => '姓名',
                'index'   => 'name'
            ],
            [
                'name'    => '产量',
                'index'   => 'capacity'
            ],
            [
                'name'    => 'Email',
                'index'   => 'email'
            ]
        ]);
    }
}
