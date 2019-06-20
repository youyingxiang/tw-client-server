<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangTwJudgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tw_judges', function (Blueprint $table) {
            $table->tinyInteger("link_state")->default(0)->comment("0 未连接 1 已连接");
            $table->string("session_id",128)->default("")->comment("当前连接用户的sessionid");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
