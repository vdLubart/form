<?php

namespace Lubart\Form;

use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'lubart.form');

        $this->publishes([
            __DIR__ . "/css/style.css" => public_path('css/lubart-form_styles.css'),
            __DIR__ . "/scss/" => resource_path('sass/lubart-form/'),
        ], "lubart-form-style");

        $this->publishes([
            __DIR__ . "/views/" => resource_path('views/lubart-form')
        ], "lubart-form-view");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
