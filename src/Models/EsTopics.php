<?php

namespace ExpertSystem\Models;

use Illuminate\Database\Eloquent\Model;

class EsTopics extends Model
{
    //
    protected $table = 'es_topics';
    protected $primaryKey = 'id';
    protected $fillable = [
        'topic_name',
        'topic_slug',
        'result_type',
        'negative_result',
        'positive_result',
        'data_result',
        'progressive_result',
        'progressive_rule',
        'stop_when_negative',
    ];
}
