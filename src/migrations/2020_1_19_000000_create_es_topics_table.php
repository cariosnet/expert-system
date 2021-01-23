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
        Schema::create('es_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('topic_name');
            $table->string('topic_slug');
            $table->enum('result_type',['pn','data','progressive'])->default('pn');
            $table->text('negative_result')->nullable(); // parameter yang dibutuhkan
            // untuk melakukan data mining di question ini misal : pertanyaan sebelumnya harus bernilai sekian
            $table->text('positive_result')->nullable();
            $table->json('data_result')->nullable();
            $table->json('progressive_result')->nullable();
            $table->json('progressive_rule')->nullable();
            $table->boolean('stop_when_negative')->default(true);
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
        Schema::dropIfExists('es_topics');
    }
}
