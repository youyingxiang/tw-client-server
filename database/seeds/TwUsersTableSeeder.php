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
            'img' => "/vendor/tw/global/face/default.png",
            'created_at' => date("Y-m-d H:i:s",time()),
            'updated_at' => date("Y-m-d H:i:s",time())
        ]);
        DB::table('tw_admin')->insert([
            'name' => "里斯",
            'phone' => '15211266576',
            'password' => Hash::make("123456"),
            'img' => "/vendor/tw/global/face/default.png",
            'created_at' => date("Y-m-d H:i:s",time()),
            'updated_at' => date("Y-m-d H:i:s",time())
        ]);
    }
}
