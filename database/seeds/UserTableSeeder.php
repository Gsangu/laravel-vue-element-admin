<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'       => 'admin',
            'email'      => 'admin@admin.com',
            'password'   => bcrypt('123456'),
            'roles'      => json_encode(['data' => ['admin']]),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        //$users = factory(App\User::class)->times(10)->make();
        //$user_array = $users->makeVisible(['password', 'remember_token'])->toArray();
        //App\User::insert($user_array);
    }
}
