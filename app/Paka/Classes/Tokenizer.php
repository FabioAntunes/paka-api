<?php namespace App\Paka\Classes;

use App\Token;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Tokenizer {

    /**
     * Authenticated user
     * @var \App\User
     */
    protected $user;


    /**
     * Authenticates the api key and sets the respective User
     *
     * @param string $apiKey
     * @return bool
     */
    public function authenticate($apiKey)
    {
        try{
            $token = Token::where('key', '=', $apiKey)->where('expires', '>',  date("Y-m-d H:i:s"))->firstOrFail();
            return $this->user = $token->user;
        }catch(ModelNotFoundException $e){
            return false;
        }

    }

    /**
     * Returns the authenticated user
     * @return \App\User
     */
    public function getUser()
    {
        return $this->user;
    }
}