<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MessageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [];
        $datas_temp=[];
        $faker = Faker\Factory::create();

        foreach (range(1, 1000) as $index) {
            $datas_temp = [
                'title'   => $faker->sentence, 
                'content' => $faker->sentence, 
                'country' => $faker->country, 
                'tel'     => $faker->e164PhoneNumber, 
                'name'    => $faker->name
            ];
            $datas[] = [
                'content'    => json_encode($datas_temp),
                'status'     => $faker->boolean,
                'ip'         => ip2long($faker->ipv4),
                'sourceUrl'  => $faker->url,
                'system'     => $faker->randomElement(['win', 'linux', 'mac'])
            ];
        }
        DB::table('messages')->insert($datas);
    }
}
