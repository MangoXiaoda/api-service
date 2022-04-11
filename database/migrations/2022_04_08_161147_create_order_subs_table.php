<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_subs', function (Blueprint $table) {
            $table->id();
            $table->string('sub_sn',50)->comment('子订单号');
            $table->unsignedBigInteger('user_id')->comment('买家id:从属关联users表');
            $table->decimal('goods_amount',10,2)->default('0.00')->comment('商品总额');
            $table->decimal('paid_amount',10,2)->default('0.00')->comment('实付总额');
            $table->tinyInteger('status')->default('0')->comment('订单状态:0正常,1取消,2删除');
            $table->tinyInteger('pay_status')->default('0')->comment('支付状态：0待付款,1已付款');
            $table->unsignedBigInteger('code')->nullable()->comment('核销码');
            $table->tinyInteger('code_status')->default('1')->comment('核销码状态：0过期,1正常');
            $table->string('remark')->nullable()->comment('备注');
            $table->tinyInteger('finish_status')->default('0')->comment('完成状态:0待完成,1部分完成,2已完成');
            $table->timestamp('finish_time')->nullable()->comment('完成时间');
            $table->softDeletes()->comment('删除时间:有值表示已删除');
            $table->timestamps();
            $table->tableComment('子订单表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_subs');
    }
};
