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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('名称');
            $table->string('image')->nullable()->comment('图片路径');
            $table->string('url')->nullable()->comment('链接');
            $table->integer('sort')->default(0)->comment('排序:默认0');
            $table->timestamp('start_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->tinyInteger('status')->default(1)->comment('状态:1正常,0关闭');
            $table->timestamps();
            $table->tableComment('banner表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
