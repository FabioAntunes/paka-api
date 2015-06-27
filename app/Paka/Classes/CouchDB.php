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

        return $this->execute($method, $url, array_merge_recursive($defaultOptions, $options));

    }


    public function authenticate()
    {
        $response = $this->executeAuth('get', '_session');
        $user = json_decode($response->getBody()->getContents());

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
        return $this->user;

    }
}