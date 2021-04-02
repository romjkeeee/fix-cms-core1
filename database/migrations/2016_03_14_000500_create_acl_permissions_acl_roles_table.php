<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAclPermissionsAclRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acl_permissions_acl_roles', function (Blueprint $table) {
            $table->integer('acl_role_id')->unsigned();
            $table->integer('acl_permission_id')->unsigned();

            $table->primary(['acl_role_id', 'acl_permission_id']);

            $table->foreign('acl_role_id', 'fk_acl_pivot_roles')
                ->references('id')
                ->on('acl_roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('acl_permission_id', 'fk_acl_pivot_permissions')
                ->references('id')
                ->on('acl_permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('acl_permissions_acl_roles');
    }
}
