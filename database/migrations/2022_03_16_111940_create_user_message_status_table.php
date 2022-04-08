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
        //查询方式
        //阅读消息列表，区分开来读取，
        //1、有指定用户的先读，即 user_message_status 表有数据的先阅读。
        //2、阅读指定组 店铺信息的店内消息，即店内会员消息，user_messages表有，user_message_status表没有，表示未读。
        //3、查询系统全平台的。
        Schema::create('user_message_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('uid')->comment('用户ID:关联user表id');
            $table->bigInteger('messages_id')->comment('消息ID:关联user_messages表id');
            $table->tinyInteger('status')->default(0)->comment('阅读状态:0未读,1已读');
            $table->timestamp('read_time')->nullable()->comment('阅读时间');
            $table->timestamp('del_time')->nullable()->comment('删除时间,有值表示已删除,阅读者删除');
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
        Schema::dropIfExists('user_message_status');
    }
};
