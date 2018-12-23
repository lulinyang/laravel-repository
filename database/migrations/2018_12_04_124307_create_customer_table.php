<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lly_customer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("role_id")->nullable()->comment("角色ID");
            $table->string("name",50)->unique()->comment("真实姓名");
            $table->string("username",50)->unique()->comment("用户名-登录名");
            $table->string('email')->unique()->comment("邮箱");
            $table->string('password')->nullable(false)->comment("密码");
            $table->string("tel",15)->nullable()->comment("电话");
            $table->string("address")->nullable()->comment("地址");
            $table->string("logo")->nullable()->default("")->comment("LOGO");
            $table->decimal("lat",10,7)->nullable()->comment("经度");
            $table->decimal("lng",10,7)->nullable()->comment("纬度");
            $table->string("remark")->nullable()->comment("备注");
            $table->tinyInteger("isusing")->default(0)->nullable()->comment("是否禁");
            $table->tinyInteger("deleted")->default(0)->nullable()->comment("是否删除，0否，1是");
            $table->dateTime("deleted_at")->nullable()->comment("删除时间");
            $table->integer("deleted_user")->nullable()->comment("删除人");
            $table->string("isusing_user")->nullable()->comment("禁用人");
            $table->string("update_user")->nullable()->comment("更新人");
            $table->dateTime("login_at")->nullable()->comment("最近一次登录时间");
            $table->string("openid",50)->nullable()->comment("微信openid");
            $table->string("wx_name",50)->nullable()->comment("微信昵称名");
            $table->string("wx_logo")->nullable()->comment("微信头像");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `lly_customer` comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lly_customer');
    }
}
