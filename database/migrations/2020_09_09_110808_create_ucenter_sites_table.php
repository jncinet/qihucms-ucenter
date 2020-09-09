<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUcenterSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ucenter_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('站点名称');
            $table->string('user_api')->comment('会员接口路径');
            $table->string('account_api')->comment('账户接口路径');
            $table->ipAddress('ip')->nullable()->comment('站点ID');
            $table->string('token')->comment('token');
            $table->string('encrypt_type', 50)->default('string')->comment('加密方式');
            $table->string('desc')->nullable()->comment('备注');
            $table->boolean('status')->default(false)->comment('状态');
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
        Schema::dropIfExists('ucenter_sites');
    }
}
