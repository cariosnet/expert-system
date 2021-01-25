<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsResponseQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_response_question', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session');
            $table->integer('question_id');
            $table->json('response');
            $table->boolean('finish_session');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('es_response_question');
    }
}
