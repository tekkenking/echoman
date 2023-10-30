<?php

if(! function_exists('echoman')) {
    function documan(string $disk = '') {
        return app('echoman', [$disk]);
    }
}
