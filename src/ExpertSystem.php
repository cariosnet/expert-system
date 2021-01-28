<?php
namespace ExpertSystem;

use ExpertSystem\Models\EsResponseQuestion;
use ExpertSystem\Models\EsTopics;
use Illuminate\Support\Facades\Log;
use ExpertSystem\Models\EsMiningQuestion;

class ExpertSystem
{
    protected $modelPath;
    public function __construct($modelPath)
    {
        $this->modelPath = $modelPath;
    }

    public function fetchQuestion($topicId, $orders = -1, $res = null,$session = null){
        //dd($izin);
        $parameter = [];
        $paramUsed = [];
        $exclude = [];
        $skipTo = null;
        $needProcess = false;
        $izin = null;
        $topic = EsTopics::find($topicId);
        $session = $session != null ? $session : session('sessionEs');
        if(empty($session)){
            $sessionName = date('ymdHis') . '-'. $topic->topic_slug;
            session(["sessionEs" => $sessionName]);
            $session = session('sessionEs');
        }
        $question = EsMiningQuestion::select('es_mining_question.*')->where('topic_id', $topicId);
        if($orders != -1){
            $question = $question->where('orders',$orders);
        }
        if($res != null) {
            $rq = $res[0]['question'];
            $answered = EsResponseQuestion::where('session',$session)->get()->pluck('id');
            //dd($answered);
            $inAnswered = str_replace('[','(',str_replace(']',')',json_encode($answered)));
            $cekNeedParam = EsMiningQuestion::select('es_mining_question.*')->whereNotNull('parameter_need')
                ->whereRaw("(parameter_need ->>'question')::integer in $inAnswered");
//            if($orders != -1){
//                $cekNeedParam = $cekNeedParam->where('orders',$orders);
//            }
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
                                if(!array_key_exists('operator',$param['response'])){
                                    foreach ($param['response'] as $par) {
                                        $respUser = $r['response'];
                                        $opr = $param['operator'];
                                        $valNeed = $param['value'];
                                        $condition = eval("return ($respUser $opr $valNeed);");
                                        //$condition = eval("return ".$r['response'] . $par['operator'] . "'" . $par['value']."';");
                                        if ($condition) {
                                            //if($r['question'] != $item->id){
                                            Log::debug('param : ' . $r['question'] . ' - item : ' . $param['question']);
                                            $parameter[] = $item->id;
                                            if ($item->need_process) {
                                                $needProcess = true;
                                            }
                                        }
                                    }
                                }else {
                                    $respUser = $r['response'];
                                    $opr = $param['response']['operator'];
                                    $valNeed = $param['response']['value'];
                                    //dd($respUser. $opr .$valNeed);
                                    $condition = eval("return ('$respUser' $opr '$valNeed');");
                                    if ($condition) {
                                        //if($r['question'] != $item->id){
                                        Log::debug('param : ' . $r['question'] . ' - item : ' . $param['question']);
                                        $parameter[] = $item->id;
                                        if ($item->need_process) {
                                            $needProcess = true;
                                        }
                                    }else{
                                        $exclude[] = $item->id;
                                        $skipTo = $item->orders + 1;
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
        if(count($exclude) > 0){
            $question = $question->whereNotIn('id',$exclude);
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
        $data['session'] = $session;
        if($skipTo != null && count($data['question']) == 0){
            return $this->fetchQuestion($topicId,$skipTo,$res,$session);
        }
        return $data;
    }

    private function checkResult($session,$data){

        $dataRequest = $data;
        foreach ($dataRequest as $item) {
            $questionId = $item['question'];
            $question = EsMiningQuestion::find($questionId);
            $topic = EsTopics::find($question->topic_id);
            $result = $this->getResult($session,$topic->topic_slug);
        }

        return $result;
    }

    public function checkpoint($session,$res){
        $dataRequest = $res;
        $i = 0;
        $return = [];
        if(count($dataRequest) > 0) {
            foreach ($dataRequest as $item) {
                $questionId = $item['question'];

                $question = EsMiningQuestion::find($questionId);

                $cek = EsResponseQuestion::where('session',$session)->where('question_id',$question->id)->first();
                //dd($cek);
                $dataResponse = ['response'=>$item['response']];
                if($cek != null){
                    EsResponseQuestion::where('session',$session)->where('question_id',$question->id)
                        ->update(['session'=>$session,'response'=>json_encode($dataResponse),'finish_session'=>false]);
                }else{
                    $answerResponse = new EsResponseQuestion();
                    $answerResponse->session = $session;
                    $answerResponse->question_id = $question->id;
                    $answerResponse->response = json_encode($dataResponse);
                    $answerResponse->finish_session = false;
                    $answerResponse->save();
                }

                if ($question->need_process && count($dataRequest) - 1 == $i) {
                    $function = $question->which_process;
                    $i++;
                    if(method_exists($this,$function)) {
                        $return = array_merge($this->$function($session,$dataRequest),$return);
                        $return = array_merge($this->fetchQuestion($question->topic_id,$question->orders+1,$dataRequest,$session),$return);
                    }else{
                        $return = array_merge($this->fetchQuestion($question->topic_id,$question->orders+1,$dataRequest,$session));
                    }
//                    else{
//                        $esProcessor = new EsProcessorHelper(null);
//                        return $esProcessor->$function($dataRequest);
//                    }
                } else {
                    $i++;
                    $return = array_merge($this->fetchQuestion($question->topic_id,$question->orders+1,$dataRequest,$session),$return);
                }
                $topic = EsTopics::find($question->topic_id);
                if((array_key_exists('result',$return) && count($return['question']) > 0) && ($topic->stop_when_negative && $topic->negative_result == $return['result'])){
                    unset($return['question']);
                    unset($return['title']);
                    unset($return['description']);
                    session()->forget('sessionEs');
                }elseif (count($return['question']) == 0 && !array_key_exists('result',$return)){
                    session()->forget('sessionEs');
                    return $this->checkResult($session,$res);
                }
                return $return;
            }
        }else{
            return [];
        }
    }

    private function getResult($session,$topic)
    {
        $dataResult = [];
        $mapingData = EsResponseQuestion::join('es_procedural_decision', 'es_response_question.question_id', 'es_procedural_decision.question_id')
            ->where('session', $session)->get();
        foreach ($mapingData as $maping) {
            $response = json_decode($maping->response);
            $mapingResponse = json_decode($maping->decision_map);
            foreach ($mapingResponse as $mapResponse) {
                if ($mapResponse->option == '=') {
                    if ($response->response == $mapResponse->value) {
                        if (empty($dataResult[$maping->decision_for]) || $mapResponse->key_level > $dataResult['key_level_'.$maping->decision_for]) {
                            $dataResult[$maping->decision_for] = $mapResponse->key;
                            $dataResult['key_level_' . $maping->decision_for] = $mapResponse->key_level;
                        }
                        break;
                    }
                } elseif ($mapResponse->option == '<') {
                    if ($response->response < $mapResponse->value) {
                        if (empty($dataResult[$maping->decision_for]) || $mapResponse->key_level > $dataResult['key_level_'.$maping->decision_for]) {
                            $dataResult[$maping->decision_for] = $mapResponse->key;
                            $dataResult['key_level_' . $maping->decision_for] = $mapResponse->key_level;
                        }
                        break;
                    }
                }elseif($mapResponse->option == '<='){
                    if ($response->response <= $mapResponse->value) {
                        if (empty($dataResult[$maping->decision_for]) || $mapResponse->key_level > $dataResult['key_level_'.$maping->decision_for]) {
                            $dataResult[$maping->decision_for] = $mapResponse->key;
                            $dataResult['key_level_' . $maping->decision_for] = $mapResponse->key_level;
                        }
                        break;
                    }
                }elseif($mapResponse->option == '>'){
                    if ($response->response > $mapResponse->value) {
                        if (empty($dataResult[$maping->decision_for]) || $mapResponse->key_level > $dataResult['key_level_'.$maping->decision_for]) {
                            $dataResult[$maping->decision_for] = $mapResponse->key;
                            $dataResult['key_level_' . $maping->decision_for] = $mapResponse->key_level;
                        }
                        break;
                    }
                }
            }
        }
        //dd($dataResult);
        return $result = [
            'result' => $dataResult[$topic]
        ];
    }
}
