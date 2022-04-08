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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->default('')->comment('设置名称');
            $table->string('code')->comment('唯一标识符');
            $table->string('value')->comment('标识符对应值');
            $table->text('remark')->nullable()->comment('补充说明');
            $table->timestamps();
            $table->tableComment('系统设置表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};
