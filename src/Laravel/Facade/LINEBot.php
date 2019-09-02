<?php

namespace LINE\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class LINEBot extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'linebot';
    }
}
