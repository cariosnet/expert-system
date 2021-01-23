<?php

namespace Zlab\ExpertSystem\Models;

use Illuminate\Database\Eloquent\Model;

class EsProceduralDecision extends Model
{
    //
    protected $table = 'es_procedural_decision';
    protected $primaryKey = 'id';
    protected $fillable = [
        'question_id',
        'decision_for',
        'decision_map',
        'created_by',
        'updated_by',
    ];
}
