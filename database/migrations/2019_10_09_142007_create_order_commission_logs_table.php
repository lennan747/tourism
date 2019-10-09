<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCommissionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_commission_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('commission_user_id'); //分给谁
            $table->foreign('commission_user_id')->references('id')->on('users');
            $table->decimal('money',10,2);
            $table->string('type'); // 分成类型 直接收客tourism_d 团队收客tourism_t 直接门店经理store_d 团队门店经理store_t 酱紫玩家 player
            $table->text('desc'); // 描述
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
        Schema::dropIfExists('order_commission_logs');
    }
}
