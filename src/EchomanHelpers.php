<?php

use Tekkenking\Echoman\Echoman;

if(! function_exists('echoman')) {
    function echoman(): Echoman
    {
        return new Echoman();
    }
}
