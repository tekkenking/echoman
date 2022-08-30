<?php

namespace Tekkenking\Echoman;

use Illuminate\Support\ServiceProvider;

class EchomanServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('echoman', function ($app) {
            return new Echoman();
        });

    }


}
