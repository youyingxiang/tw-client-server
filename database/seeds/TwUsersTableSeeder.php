<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class TwUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("truncate table tw_admin");
        DB::table('tw_admin')->insert([
            'name' => "游兴祥",
            'phone' => '18123670736',
            'password' => Hash::make("123456"),
            'img' => "vendor/tw/global/face/default.png",
            'created_at' => "2019-05-22 08:52:22",
            'updated_at' => date("Y-m-d H:i:s",time())
        ]);
    }
}
