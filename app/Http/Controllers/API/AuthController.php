<?php namespace App\Http\Controllers\API;

use Tokenizer;
use App\Http\Requests\AuthRequest;
use App\Paka\Transformers\TokensTransformer;

class AuthController extends ApiController {

    /**
     * @var TokensTransformer
     */
    protected $tokensTransformer;


    public function __construct()
    {
        $this->middleware('auth.token', ['except' => ['login']]);
        $this->tokensTransformer = new TokensTransformer();

    }

    /**
     * Authenticates a user, if succeed returns his token, else returns a 401
     *
     * @param AuthRequest $request
     * @return \Response
     */
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $device = $request->only('model', 'platform', 'uuid', 'version');
        if(Tokenizer::authWithCredentials($credentials))
        {
            return $this->respond($this->tokensTransformer->getToken($device));
        }else{
            $this->setStatusCode(401);
            return $this->respondWithError('Wrong credentials');
        }

    }
}