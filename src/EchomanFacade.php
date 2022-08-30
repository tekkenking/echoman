<?php

namespace Tekkenking\Echoman;

use Illuminate\Support\Facades\Facade;

class EchomanFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'echoman'; }
}
