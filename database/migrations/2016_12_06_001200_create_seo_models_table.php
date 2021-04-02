<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('model_name');
            $table->integer('model_id')->unsigned();
            $table->string('locale', 2);
            $table->string('key');
            $table->text('value');

            $table->index(['model_name', 'model_id'], 'index_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seo_models');
    }
}
