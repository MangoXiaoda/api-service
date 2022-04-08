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
        Schema::create('goods_category', function (Blueprint $table) {
            $table->id();
            $table->string('c_name', 30)->comment('商品分类名称');
            $table->tinyInteger('c_status')->default(1)->comment('状态:1正常,0关闭');
            $table->integer('c_sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->tableComment('商品分类表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_category');
    }
};
