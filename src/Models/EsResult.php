<?php

namespace ExpertSystem\Models;

use Illuminate\Database\Eloquent\Model;

class EsResult extends Model
{
    //
    protected $table = 'es_result_collection';
    protected $primaryKey = 'id';
    protected $fillable = [
        'session',
        'result'
    ];
}
