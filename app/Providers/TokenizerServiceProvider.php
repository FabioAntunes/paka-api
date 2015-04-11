<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TokenizerServiceProvider extends ServiceProvider{

    public function register()
    {
        $this->app->bind('tokenizer', function(){
            return new \App\Paka\Classes\Tokenizer;
        });
    }
}