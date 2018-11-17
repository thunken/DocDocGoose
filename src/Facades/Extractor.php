<?php

namespace Thunken\DocDocGoose\Facades;

use Illuminate\Support\Facades\Facade;

class Extractor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Thunken\DocDocGoose\Tools\Extractor::class;
    }
}