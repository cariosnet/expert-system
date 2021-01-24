<?php


namespace ExpertSystem;


use Illuminate\Support\Facades\Auth;
use ExpertSystem\Models\EsMiningQuestion;
use ExpertSystem\Models\EsProceduralDecision;
use ExpertSystem\Models\EsTopics;

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

    /**
     * @return $this
     */
    public function when(): self
    {
        $this->index++;
        $this->decision_map[$this->index] = [];
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function is($value): self
    {
        $this->decision_map[$this->index]['option'] = "=";
        $this->decision_map[$this->index]['value'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function greaterThan($value): self
    {
        $this->decision_map[$this->index]['option'] = ">";
        $this->decision_map[$this->index]['value'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function lowerThan($value): self
    {
        $this->decision_map[$this->index]['option'] = "<";
        $this->decision_map[$this->index]['value'] = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function maximum($value): self
    {
        $this->decision_map[$this->index]['option'] = "<=";
        $this->decision_map[$this->index]['value'] = $value;
        return $this;
    }

    /**
     * @param $result
     * @param null $keyIndex
     * default value of keyIndex is depend by the order of calling function "when()"
     * @return $this
     */
    public function setResult($result, $keyIndex = null): self
    {
        $this->decision_map[$this->index]['key_level'] = $keyIndex == null ? $this->index + 1 : $keyIndex;
        $this->decision_map[$this->index]['key'] = $result;

        return $this;
    }

    /**
     * @return mixed
     */
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
