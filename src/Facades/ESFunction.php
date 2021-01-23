<?php


namespace Zlab\ExpertSystem\Facades;


use Illuminate\Support\Facades\Facade;

class ESFunction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Zlab\ExpertSystem\ExpertSystem::class;
    }
}
