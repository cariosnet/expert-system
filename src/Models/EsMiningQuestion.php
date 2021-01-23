<?php

namespace Zlab\ExpertSystem\Models;

use Illuminate\Database\Eloquent\Model;

class EsMiningQuestion extends Model
{
    //
    protected $table = 'es_mining_question';
    protected $primaryKey = 'id';

    protected $fillable = [
        'topic_id',
        'question',
        'question_slug',
        'parameter_need',
        'answer_choice',
        'need_process',
        'which_process',
        'answer_type',
        'created_by',
        'updated_by',
        'additional_info',
        'orders'
    ];
}
