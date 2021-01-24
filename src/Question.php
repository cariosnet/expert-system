<?php


namespace ExpertSystem;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use ExpertSystem\Models\EsMiningQuestion;

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

    /**
     * @param string $question
     * @param int $orders
     * @param string $answerType
     * text | switch | radio | select | multiple | number | date
     * @param string $additionalInfo
     * @return $this
     */
    public function make($question, $orders, $answerType, $additionalInfo = ""): self
    {
        $this->created_by = Auth::user() != null ? Auth::user()->id : -1;
        $this->question = $question;
        $this->question_slug = str_replace(" ", "-", strtolower($question));
        $this->orders = $orders;
        $this->answer_type = $answerType;
        $this->additional_info = $additionalInfo;
        return $this;
    }

    /**
     * @param $id
     * @param string $question
     * @param int $orders
     * @param string $answerType
     * text | switch | radio | select | multiple | number | date
     * @param string $additionalInfo
     * @return $this
     */
    public function modify($id, $question, $orders, $answerType, $additionalInfo = ""): self
    {
        $this->updated_by = Auth::user() != null ? Auth::user()->id : -1;
        $this->id = $id;
        $this->question = $question;
        $this->question_slug = str_replace(" ", "-", strtolower($question));
        $this->orders = $orders;
        $this->answer_type = $answerType;
        $this->additional_info = $additionalInfo;
        return $this;
    }

    /**
     * @param string $functionName
     * fetchQuestion | checkResult
     * @return $this
     */
    public function processThisQuestion($functionName): self
    {
        $this->need_process = true;
        $this->which_process = $functionName;
        return $this;
    }

    /**
     * @param $questionId
     * @param string $operator
     * = | != | < | > | <= | >=
     * @param $value
     * @return $this
     */
    public function setDependentQuestion($questionId, $operator, $value): self
    {
        $json = [
            "question" => $questionId,
            "response" => [
                "operator" => $operator,
                "value" => $value
            ]
        ];
        $this->parameter_need = json_encode($json);
        return $this;
    }

    /**
     * @param $jsonOption
     * for select type question : [{id: "1", name: "option1"},{id: "2", name: "option2"}]
     * for radio type question : {"yes": "Yes", "no": "No"}
     * @return $this
     */
    public function setOption($jsonOption): self
    {
        $this->answer_choice = json_encode($jsonOption);
        return $this;
    }

    /**
     * @return mixed
     */
    public function build()
    {
        try {

            if ($this->id != null) {
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
            } else {
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
            }
            return $data;
        }catch (QueryException $e){
            throw  $e;
        }
    }
}
