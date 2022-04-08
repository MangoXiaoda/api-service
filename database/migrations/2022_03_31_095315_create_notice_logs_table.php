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
        Schema::create('notice_logs', function (Blueprint $table) {
            $table->id();

            $table->string('nt_type', 15)
                ->default('')
                ->comment('通知类型，对应 NoticeConfig文件的 getNoticeTypeArray 方法');

            $table->bigInteger('to_uid')
                ->default(0)
                ->comment('接收通知者的用户id');

            $table->string('about_id', 50)
                ->default('')
                ->comment('相关编号');

            $table->text('nt_data')
                ->nullable()
                ->comment('通知相关数据');

            $table->tinyInteger('nt_status')
                ->default(0)
                ->comment('通知状态：0 = 未发送，1 = 已发送');

            $table->timestamps();
            $table->tableComment('通知记录表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice_logs');
    }
};
