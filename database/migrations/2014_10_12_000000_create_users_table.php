<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acl_role_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            //$table->string('otp_secret', 40);
            $table->string('avatar_file');
            $table->rememberToken();
            $table->boolean('active');
            $table->integer('last_login')->unsigned();
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
        Schema::drop('users');
    }
}
