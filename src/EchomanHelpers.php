<?php

if(! function_exists('echoman')) {
    function echoman(string $disk = '') {
        return app('echoman', [$disk]);
    }
}
