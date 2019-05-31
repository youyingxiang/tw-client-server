<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tw_admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->default('')->comment("用户昵称");
            $table->string('phone',20)->unique()->default('')->comment('手机号码');
            $table->string('img',300)->default('')->comment("图像");
            $table->string('password',60)->default('')->comment("用户密码");
            $table->string('qq',60)->default('')->comment("qq");
            $table->string('email',60)->default('')->comment("邮箱");
            $table->string('wechat',60)->default('')->comment("微信");
            $table->string('remember_token', 100)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tw_admin');
    }
}
