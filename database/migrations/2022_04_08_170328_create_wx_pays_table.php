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
        Schema::create('wx_pays', function (Blueprint $table) {
            $table->id();

            $table->string('sub_sn', 50)
                ->comment('子订单号');

            $table->unsignedBigInteger('user_id')
                ->index()
                ->comment('用户id:从属关联users表');

            $table->string('app_id', 30)
                ->comment('微信小程序app_id');

            $table->string('time_stamp', 30)
                ->comment('UNIX时间戳');

            $table->string('nonce_str', 30)
                ->comment('下单接口返回的随机字符串');

            $table->string('package', 80)
                ->comment('将prepay_id=与prepay_id拼接而成的字符串');

            $table->string('sign_type', 20)
                ->comment('签名算法,通常为MD5');

            $table->string('pay_sign', 50)
                ->comment('签名');

            $table->softDeletes()->comment('删除时间:有值表示已删除');
            $table->timestamps();
            $table->tableComment('微信支付表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wx_pays');
    }
};
