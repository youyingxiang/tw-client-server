<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwPayOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tw_pay_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no',128)->default('')->comment("订单号");
            $table->string('order_info',128)->default('')->comment('订单信息');
            $table->tinyInteger('pay_type')->default(1)->comment("支付类型 1 微信 2支付宝");
            $table->tinyInteger('pay_state')->default(0)->comment("支付状态 0 未支付 2已支付");
            $table->decimal("pay_amount",8,2)->default(0.00)->comment('支付金额');
            $table->integer("admin_id")->default(0)->comment('所属用户');
            $table->integer("activity_id")->default(0)->comment('所属活动');
            $table->index('admin_id');
            $table->index('order_no');
            $table->index('activity_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('tw_player', function (Blueprint $table) {
            $table->integer("sort")->default(0)->comment('排序');
            $table->integer("activity_id")->default(0)->comment('所属活动')->change();
            $table->integer("admin_id")->default(0)->comment('所属用户')->change();
            $table->decimal("score",8,2)->default(0.00)->comment('最终得分')->change();
        });
        Schema::table('tw_judges', function (Blueprint $table) {
            $table->integer("activity_id")->default(0)->comment('所属活动')->change();
            $table->integer("admin_id")->default(0)->comment('所属用户')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tw_pay_order');
    }
}
