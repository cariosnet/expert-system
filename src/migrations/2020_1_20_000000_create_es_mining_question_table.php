<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsMiningQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_mining_question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id');
            $table->string('question');
            $table->string('question_slug');
            $table->json('parameter_need')->nullable(); // parameter yang dibutuhkan
            // untuk melakukan data mining di question ini misal : pertanyaan sebelumnya harus bernilai sekian
            $table->json('answer_choice')->nullable();
            $table->boolean('need_process')->default(false);
            $table->string('which_process')->nullable();
            $table->string('answer_type');
            $table->string('created_by',25)->nullable();
            $table->string('updated_by',25)->nullable();
            $table->text('additional_info')->nullable();
            $table->integer('orders')->nullable();
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
        Schema::dropIfExists('es_mining_question');
    }
}
