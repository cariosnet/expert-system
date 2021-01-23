<?php


namespace Zlab\ExpertSystem;


use Illuminate\Support\Facades\Auth;
use Zlab\ExpertSystem\Models\EsMiningQuestion;
use Zlab\ExpertSystem\Models\EsProceduralDecision;
use Zlab\ExpertSystem\Models\EsTopics;

class Decision
{
    private $decision_map = [];
    private $question, $decision_for;
    private $index = -1;

    public function __construct(EsMiningQuestion $question)
    {
        $this->decision_for = EsTopics::find($question->topic_id)->topic_slug;
        $this->question = $question->id;
    }

    public function when()
    {
        $this->index++;
        $this->decision_map[$this->index] = [];
        return $this;
    }

    public function is($value)
    {
        $this->decision_map[$this->index]['option'] = "=";
        $this->decision_map[$this->index]['value'] = $value;
    }

    public function greaterThan($value)
    {
        $this->decision_map[$this->index]['option'] = ">";
        $this->decision_map[$this->index]['value'] = $value;
    }

    public function lowerThan($value)
    {
        $this->decision_map[$this->index]['option'] = "<";
        $this->decision_map[$this->index]['value'] = $value;
    }

    public function maximum($value)
    {
        $this->decision_map[$this->index]['option'] = "<=";
        $this->decision_map[$this->index]['value'] = $value;
    }

    public function setResult($result, $keyIndex = null)
    {
        $this->decision_map[$this->index]['key_level'] = $keyIndex == null ? $this->index + 1 : $keyIndex;
        $this->decision_map[$this->index]['key'] = $result;
    }

    public function build()
    {
        $d = EsProceduralDecision::create([
            'question_id' => $this->question,
            'decision_for' => $this->decision_for,
            'decision_map' => json_encode($this->decision_map),
            'created_by' => Auth::user()->id,
        ]);
        return EsProceduralDecision::find($d);
    }
}
