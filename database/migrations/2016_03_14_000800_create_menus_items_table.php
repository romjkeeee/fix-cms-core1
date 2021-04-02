<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned();
            $table->integer('parent_id')->unsigned();

            $locales = ['en'];
            foreach ($locales as $locale) {
                $table->string('name_' . $locale, 255);
            }
            $table->string('url', 255);
            $table->tinyInteger('target')->default(0);
            $table->integer('sort')->unsigned();

            $table->index('sort', 'menus_items_sort');

            $table->foreign('menu_id', 'fk_menu_items_menus')
                ->references('id')
                ->on('menus')
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
        Schema::drop('menus_items');
    }
}
