<?php

namespace Larrock\ComponentBlocks\Facades;

use Illuminate\Support\Facades\Facade;

class LarrockBlocks extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'larrockblocks';
    }

}