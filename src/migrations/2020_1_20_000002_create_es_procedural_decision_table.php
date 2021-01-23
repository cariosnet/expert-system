<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsProceduralDecisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('es_procedural_decision', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->string('decision_for'); //misal wilayah untuk menentukan wilayah decision,or izin untuk menentukan decision izin
            $table->json('decision_map');//in case wilayah isinya parameter per wilayah mengacu sama pertanyaan contoh pertanyaan luas lahan (max) : {kelurahan: 100,kecamatan:200}
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('es_procedural_decision');
    }
}
