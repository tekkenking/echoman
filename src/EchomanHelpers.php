<?php

use Tekkenking\Echoman\Echoman;

if(! function_exists('echoman')) {
    /**
     * @return Echoman
     */
    function echoman(): Echoman
    {
        return new Echoman();
    }
}
