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
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tw_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',256)->default('')->comment("活动名称");
            $table->string('logo',300)->default('')->comment("图像");
            $table->tinyInteger('score_type')->default(1)->comment("评分方式 1:平均 2 去掉最大最小");
            $table->string('banner',300)->default('')->comment("活动背景");
            $table->integer("days")->default(3)->comment('活动天数');
            $table->tinyInteger('level')->default(1)->comment("活动级别 1 普通活动 2高级活动");
            $table->integer("admin_id")->default(3)->comment('所属用户');
            $table->softDeletes();
            $table->timestamps();
            $table->index('admin_id');
        });

        Schema::create('tw_player', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->default('')->comment("选手名称");
            $table->string('img',300)->default('')->comment("图像");
            $table->integer("score")->default(0)->comment('最终得分');
            $table->tinyInteger('push_state')->default(0)->comment("推送状态 0 未推送 1 已推送");
            $table->integer("activity_id")->default(3)->comment('所属活动');
            $table->integer("admin_id")->default(3)->comment('所属用户');
            $table->index('admin_id');
            $table->index('activity_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tw_judges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->default('')->comment("评委姓名");
            $table->string('img',300)->default('')->comment("图像");
            $table->integer("activity_id")->default(3)->comment('所属活动');
            $table->integer("admin_id")->default(3)->comment('所属用户');
            $table->index('admin_id');
            $table->index('activity_id');
            $table->softDeletes();
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
        Schema::dropIfExists('tw_admin');       // 后台用户
        Schema::dropIfExists('tw_activity');    // 活动
        Schema::dropIfExists('tw_player');      // 参数选手
        Schema::dropIfExists('tw_judges');      // 评委
    }
}
