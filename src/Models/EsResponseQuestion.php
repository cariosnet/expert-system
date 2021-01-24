<?php

namespace ExpertSystem\Models;

use Illuminate\Database\Eloquent\Model;

class EsResponseQuestion extends Model
{
    //
    protected $table = 'es_response_question';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'session',
        'question_id',
        'response',
        'finish_session'
    ];
}
