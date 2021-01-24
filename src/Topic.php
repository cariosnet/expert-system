<?php


namespace ExpertSystem;


use ExpertSystem\Models\EsTopics;
use Illuminate\Database\QueryException;

class Topic
{
    private $topic_name, $topic_slug, $result_type = null, $negative_result = null, $positive_result = null;
    private $progressive_rule = null, $data_result = null, $progressive_result = null, $stop_when_negative = false;


    public function __construct($topic)
    {
        $this->topic_name = $topic;
        $this->topic_slug = str_replace(" ", "_", strtolower($topic));
    }


    public function builder($type = 'pn'): self
    {

        return $this;
    }

    /**
     * @param $positiveResult
     * @param $negativeResult
     * @param bool $stopWhenNegative
     * @return $this
     */
    public function usePositiveNegativeType($positiveResult, $negativeResult, $stopWhenNegative = false): self
    {
        $this->result_type = 'pn';
        $this->negative_result = $negativeResult;
        $this->positive_result = $positiveResult;
        $this->stop_when_negative = $stopWhenNegative;
        return $this;
    }

    public function build()
    {
        try {
            $insert = EsTopics::create([
                'topic_name' => $this->topic_name,
                'topic_slug' => $this->topic_slug,
                'result_type' => $this->result_type,
                'negative_result' => $this->negative_result,
                'positive_result' => $this->positive_result,
                'data_result' => $this->data_result,
                'progressive_result' => $this->progressive_result,
                'progressive_rule' => $this->progressive_rule,
                'stop_when_negative' => $this->stop_when_negative,
            ]);
            return $insert;
        }catch (QueryException $e){
            throw $e;
        }

    }
}
