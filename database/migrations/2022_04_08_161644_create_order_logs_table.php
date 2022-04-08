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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sub_sn',50)->comment('子订单号:从属于order_sub表sub_sn');
            $table->string('action')->comment('操作类型');
            $table->unsignedBigInteger('operator_id')->default('0')->comment('操作者ID:0表示系统自动');
            $table->string('operator_name',50)->comment('操作者名称');
            $table->tinyInteger('operator_type')->default('1')->comment('操作者类型:1系统,2后台管理员');
            $table->string('msg',500)->nullable()->comment('日志详情');
            $table->string('client_ip',30)->nullable()->comment('操作IP地址');
            $table->timestamps();
            $table->tableComment('订单日志表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_logs');
    }
};
