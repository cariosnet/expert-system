<?php
namespace ExpertSystem;

use Illuminate\Support\Facades\Log;
use ExpertSystem\Models\EsMiningQuestion;

class ExpertSystem
{
    protected $modelPath;
    public function __construct($modelPath)
    {
        $this->modelPath = $modelPath;
    }

    public function fetchQuestion($topicId, $orders = -1, $res = null){
        //dd($izin);
        $parameter = [];
        $paramUsed = [];
        $needProcess = false;
        $izin = null;
        $idDefault = '123123';
        $question = EsMiningQuestion::select('es_mining_question.*')->where('topic_id', $topicId);
        if($orders != -1){
            $question = $question->where('orders',$orders);
        }
        if($res != null) {
            $cekNeedParam = EsMiningQuestion::select('es_mining_question.*')->whereNotNull('parameter_need');
            if($orders != -1){
                $cekNeedParam = $cekNeedParam->where('orders',$orders);
            }
            $cekNeedParam = $cekNeedParam->get();
            //dd($cekNeedParam);
            if (count($cekNeedParam) > 0) {
                foreach ($cekNeedParam as $item) {
                    $param = json_decode($item->parameter_need, true);
                    if(array_key_exists('question',$param)) {
                        foreach ($res as $r) {
                            if ($param['question'] == $r['question']) {
                                if (is_array($r['response'])) {
                                    foreach ($r['response'] as $it) {
                                        dd($it);
                                    }
                                }
                                if(is_array($param['needed_response'])){
                                    foreach ($param['needed_response'] as $par) {
                                        if ($par == $r['response']) {
                                            //if($r['question'] != $item->id){
                                            Log::debug('param : ' . $r['question'] . ' - item : ' . $param['question']);
                                            $parameter[] = $item->id;
                                            if ($item->need_process) {
                                                $needProcess = true;
                                            }
                                        }
                                    }
                                }else {
                                    if ($param['needed_response'] == $r['response']) {
                                        //if($r['question'] != $item->id){
                                        Log::debug('param : ' . $r['question'] . ' - item : ' . $param['question']);
                                        $parameter[] = $item->id;
                                        if ($item->need_process) {
                                            $needProcess = true;
                                        }
                                    }
                                }
                            }
                            $paramUsed[] = $r['question'];
                            if ($r['question'] == $item->id) {
                                if ($item->need_process) {
                                    $needProcess = false;
                                }
                            }
                        }
                    }

                }

                //dd($parameter);
            }
        }else{
            $question = $question->whereNull('parameter_need');
        }
        if(count($parameter) > 0) {
            if($needProcess){
                $question = $question->whereIn('id',$parameter);
            }else{
                $question = $question->where(function ($query) use ($parameter,$izin){
                    $query->whereIn('id',$parameter);
                });
            }
            $question = $question->whereNotIn('id',$paramUsed);
        }

        $question = $question->orderBy('orders','asc');
        //dd($question->toSql());
        $data['question'] = $question->get()->toArray();
        //dd($data);
        $data['question'] = collect($data['question'])->map(function($question){
            $answerMap = json_decode($question['answer_choice']);
            $data = $answerMap;
            if($data != null) {
                //dd($answerMap);
                if(!is_array($answerMap)) {
                    if (property_exists($answerMap, 'model')) {
                        $answerChoice = $this->modelPath . $answerMap->model;
                        $data = $answerChoice::select(['id', 'name']);
                        if(property_exists($answerMap,'opsi')){
                            foreach ($answerMap->opsi as $option) {
                                $query = $option->query;
                                $value = $option->value;
                                $data = $data->$query($option->condition_field,$option->operator,$value);
                            }
                        }
                        $data = $data->get();
                    }
                }
            }
            $question['answer_choice'] = $data;
            return $question;
        });
        $data['title'] = 'Pertanyaan '. ($orders != -1 ? $orders : "");
        $data['description'] = "Tolong Lengkapi Pertanyaan Berikut";

        return $data;
    }
}
