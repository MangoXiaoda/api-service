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
        Schema::create('order_goods', function (Blueprint $table) {
            $table->id();
            $table->string('sub_sn',50)->comment('子订单号:从属于order_sub表sub_sn');
            $table->unsignedBigInteger('goods_id')->comment('商品编号id:从属于goods表id');
            $table->decimal('goods_price',10,2)->default('0.00')->comment('销售售价');
            $table->bigInteger('buy_num')->default('0')->comment('购买数量');
            $table->decimal('real_price',10,2)->default('0.00')->comment('应付金额');
            $table->string('title')->comment('商品名称');
            $table->string('thumb')->comment('商品主图');
            $table->softDeletes()->comment('删除时间:有值表示已删除');
            $table->timestamps();
            $table->tableComment('订单商品表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_goods');
    }
};
