<?php namespace App\Paka\Classes;

use HttpClient;

class CouchDB {

    protected $httpClient;
    protected $token;
    protected $user;

    public function __construct(){
        $this->httpClient = new HttpClient([
            // Base URI is used with relative requests
            'base_uri' => 'http://localhost:5984/',
            'cookies' => true
        ]);
    }

    public function execute($method, $url, $options)
    {
        return $this->httpClient->{$method}($url, $options);

    }

    public function executeAuth($method, $url, $options=[])
    {
        $defaultOptions = [
            'headers' => [
                'Accept'=> '*/*',
                'Cookie'=> 'AuthSession='.$this->token
            ]
        ];
        $response = $this->execute($method, $url, array_merge_recursive($defaultOptions, $options));
        return $this->parseStream($response);

    }

    public function executeAdmin($method, $url, $options=[]){
        $httpClient = new HttpClient();
        $response = $httpClient->{$method}(env('COUCHDB_ADMIN').$url, $options);
        return $this->parseStream($response);
    }


    public function authenticate()
    {
        $user = $this->executeAuth('get', '_session');

        if ($user->userCtx->name != null)
        {
            $this->user = $user->userCtx;

            return $this->user;
        }

        return false;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;

    }

    public function getToken()
    {
        return $this->token;

    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user ? $this->user : $this->authenticate();
    }

    public function parseStream($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function setHttpClient(HttpClient $httpClient){
        $this->httpClient = $httpClient;
    }
}