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
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->default('')->comment('用户真实姓名');
            $table->string('nickname',128)->default('')->comment('用户微信昵称');
            $table->tinyInteger('gender')->default(1)->comment('性别:1男,0女,3未知');
            $table->string('avatar')->default('')->comment('头像路径');
            $table->string('id_card',20)->default('')->comment('身份证号');
            $table->string('phone',20)->default('')->comment('用户手机号');
            $table->string('weapp_openid',50)->default('')->comment('用户微信openid');
            $table->string('wx_unionid',50)->comment('微信开放平台统一用户id');
            $table->string('wx_session_key',50)->default('')->comment('微信session key:获取用户信息时使用');
            $table->tinyInteger('status')->default(1)->comment('账号状态:1正常,-1禁用');
            $table->timestamps();
            $table->tableComment('用户表');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
