<?php namespace App\Http\Controllers\V2;

use CouchDB;
use Request;


class SyncController extends ApiController {

    public function __construct()
    {
//        $this->middleware('couch.auth');
    }

    /**
     * Sync with CouchDb
     *
     * @return \Response
     */
    public function index()
    {
//        $user = CouchDB::getUser();
        $url = str_replace(url().'/api/v2/sync', "", Request::fullUrl());
        $response = CouchDB::executeAuth(Request::method(), $url);
        return $this->setStatusCode($response->getStatusCode())->respond($this->parseStream($response));
    }
}