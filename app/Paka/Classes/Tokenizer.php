<?php namespace App\Paka\Classes;

use App\Token;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Tokenizer {

    protected $user;

    public function authenticate($apiKey)
    {
        try{
            $token = Token::where('key', '=', $apiKey)->where('expires', '>',  date("Y-m-d H:i:s"))->firstOrFail();

            return $this->user = $token->user;
        }catch(ModelNotFoundException $e){
            return false;
        }

    }
}