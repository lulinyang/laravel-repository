<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLlyPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lly_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name",64)->comment("权限名称");
            $table->string("controller",64)->comment("控制器");
            $table->string("action",64)->comment("方法");
            $table->string("description",64)->comment("描述");
            $table->integer("pid")->nullable()->comment("父ID 0-顶级");
            $table->tinyInteger("level")->default(0)->nullable()->comment("权限级别：0-顶级 1-一级");
            $table->tinyInteger("deleted")->default(0)->nullable()->comment("是否删除，0否，1是");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `lly_permissions` comment '权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lly_permissions');
    }
}
