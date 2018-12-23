<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLlyRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lly_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name",50)->comment("角色名");
            $table->string("description",50)->comment("角色描述");
            $table->tinyInteger("deleted")->default(0)->nullable()->comment("是否删除，0否，1是");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `lly_roles` comment '角色表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lly_roles');
    }
}
