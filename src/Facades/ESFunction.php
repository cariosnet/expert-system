<?php


namespace ExpertSystem\Facades;


use Illuminate\Support\Facades\Facade;
use ExpertSystem\ExpertSystem;

class ESFunction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ExpertSystem::class;
    }
}
