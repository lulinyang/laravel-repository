<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lly_role_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("role_id")->nullable()->comment("角色ID");
            $table->integer("permission_id")->nullable()->comment("权限ID");
            $table->tinyInteger("deleted")->default(0)->nullable()->comment("是否删除，0否，1是");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `lly_role_permissions` comment '角色->权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lly_role_permissions');
    }
}
