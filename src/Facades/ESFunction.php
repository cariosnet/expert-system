<?php


namespace ExpertSystem\Facades;


use Illuminate\Support\Facades\Facade;
use ExpertSystem\ExpertSystem;


/**
 * Class ESFunction
 * @package ExpertSystem\Facades
 * @method static array fetchQuestion(int $topicId,int $orders = -1, array $res = null, string $session = null)
 * @method static array checkpoint(string $session, array $res)
 * @method static array checkResult(string $session, array $res)
 */
class ESFunction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ExpertSystem::class;
    }
}
