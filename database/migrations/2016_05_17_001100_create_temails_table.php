<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// TODO: @new_version rename to email_templates
class CreateTemailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('desc', 255);
            $table->string('from', 255);
            $table->string('to', 255);

            $table->string('subject', 255);
            $table->text('body');

            $locales = ['en'];
            foreach ($locales as $locale) {
                $table->string('subject_' . $locale, 255);
                $table->text('body_' . $locale);
            }

            $table->boolean('layout');
            $table->boolean('html');
            $table->integer('modified')->unsigned();

            $table->boolean('to_admin')->default(0);
            $table->boolean('to_d_admin')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('temails');
    }
}
