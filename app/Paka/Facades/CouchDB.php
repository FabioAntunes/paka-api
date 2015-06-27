<?php namespace App\Paka\Facades;
use Illuminate\Support\Facades\Facade;

class CouchDB extends Facade {
    protected static function getFacadeAccessor() { return 'CouchDB'; }
}