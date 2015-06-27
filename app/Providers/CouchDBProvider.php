<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CouchDBProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('CouchDB', function ()
        {
            return new \App\Paka\Classes\CouchDB;
        });
    }
}