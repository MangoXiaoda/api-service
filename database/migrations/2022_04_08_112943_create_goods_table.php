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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')
                ->default(0)
                ->comment('商品分类id:从属关联 goods_category 表');
            $table->string('title')
                ->comment('商品名称');
            $table->string('thumb')
                ->comment('商品主图');
            $table->string('unit')
                ->nullable()
                ->comment('商品单位');
            $table->text('content')
                ->nullable()
                ->comment('商品详情');
            $table->string('goods_sn')
                ->nullable()
                ->comment('商品编号');
            $table->decimal('price',10,2)
                ->default('0.00')
                ->comment('商品售价');
            $table->decimal('cost_price',10,2)
                ->default('0.00')
                ->comment('商品成本价');
            $table->integer('total')
                ->default(0)
                ->comment('商品库存');
            $table->integer('sales')
                ->default(0)
                ->comment('已售数量');
            $table->tinyInteger('status')
                ->default(0)
                ->comment('状态:0-下架,1-上架');
            $table->integer('view_count')
                ->default(0)
                ->comment('查看次数');
            $table->softDeletes()->comment('删除时间:有值表示已删除');
            $table->timestamps();
            $table->tableComment('商品表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
};
