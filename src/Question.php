<?php


namespace Zlab\ExpertSystem;


use Illuminate\Support\Facades\Auth;
use Zlab\ExpertSystem\Models\EsMiningQuestion;

class Question
{
    private $id = null;
    private $question = null;
    private $question_slug = null;
    private $parameter_need = null;
    private $answer_choice = null;
    private $need_process = false;
    private $which_process = null;
    private $answer_type = null;
    private $created_by = null;
    private $updated_by = null;
    private $additional_info = null;
    private $orders = null;
    private $topic_id = null;

    public function __construct($topicId)
    {
        $this->topic_id = $topicId;
    }

    public function make($question, $orders, $answerType, $additionalInfo = ""): self
    {
        $this->created_by = Auth::user()->id;
        $this->question = $question;
        $this->question_slug = str_replace(" ", "-", strtolower($question));
        $this->orders = $orders;
        $this->answer_type = $answerType;
        $this->additional_info = $additionalInfo;
        return $this;
    }

    public function modify($id, $question, $orders, $answerType, $additionalInfo = "")
    {
        $this->updated_by = Auth::user()->id;
        $this->id = $id;
        $this->question = $question;
        $this->question_slug = str_replace(" ", "-", strtolower($question));
        $this->orders = $orders;
        $this->answer_type = $answerType;
        $this->additional_info = $additionalInfo;
    }

    public function processThisQuestion($functionName): self
    {
        $this->need_process = true;
        $this->which_process = $functionName;
        return $this;
    }

    public function setDependentQuestion($questionId, $operator, $value)
    {
        $json = [
            "question" => $questionId,
            "response" => [
                "operator" => $operator,
                "value" => $value
            ]
        ];
        $this->parameter_need = json_encode($json);
    }

    public function setOption($jsonOption)
    {
        $this->answer_choice = json_encode($jsonOption);
    }

    public function build()
    {
        if($this->id != null){
            $data = EsMiningQuestion::find($this->id)->update([
                'topic_id' => $this->topic_id,
                'question' => $this->question,
                'question_slug' => $this->question_slug,
                'parameter_need' => $this->parameter_need,
                'answer_choice' => $this->answer_choice,
                'need_process' => $this->need_process,
                'which_process' => $this->which_process,
                'answer_type' => $this->answer_type,
                'created_by' => $this->created_by,
                'updated_by' => $this->updated_by,
                'additional_info' => $this->additional_info,
                'orders' => $this->orders,
            ]);
        }
        $data = EsMiningQuestion::create([
            'topic_id' => $this->topic_id,
            'question' => $this->question,
            'question_slug' => $this->question_slug,
            'parameter_need' => $this->parameter_need,
            'answer_choice' => $this->answer_choice,
            'need_process' => $this->need_process,
            'which_process' => $this->which_process,
            'answer_type' => $this->answer_type,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'additional_info' => $this->additional_info,
            'orders' => $this->orders,
        ]);
        return EsMiningQuestion::find($data);
    }
}
