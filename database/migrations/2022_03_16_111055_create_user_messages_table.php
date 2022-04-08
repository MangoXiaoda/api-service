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
        Schema::create('user_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type')->default(1)->comment('消息类型:1系统通知,2用户,3评论,4兑换');
            //消息类组:
            //0全平台,全平台用户均可看到，属于公告，用户阅读后才写入一条状态表已读数据
            //1指定用户--与user_message_status表一对多关系,生成一条直接写入user_message_status表
            $table->tinyInteger('to_group')->default(0)->comment('消息类组:0全平台,1指定用户');
            $table->string('introduction')->nullable()->comment('消息简介说明');
            $table->text('details')->nullable()->comment('详情Json数据,主要包含:系统,用户,评论回复,兑换');
            $table->timestamp('del_time')->nullable()->comment('删除时间-有值表示已删除,删除只有发布者有权限');
            $table->timestamps();
            $table->tableComment('用户消息表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_messages');
    }
};
