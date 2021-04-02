<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->default(null);

            $locales = ['en'];
            foreach ($locales as $locale) {
                $table->text('content_' . $locale);
                $table->string('title_' . $locale, 255);
            }

            $table->string('url', 255);
            $table->integer('created')->unsigned()->nullable()->default(null);
            $table->integer('modified')->unsigned()->nullable()->default(null);
            $table->boolean('active');

            $table->unique('url', 'content_unique_url');
            $table->index('user_id', 'content_author');

            $table->foreign('user_id', 'fk_contents_users')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contents');
    }
}
