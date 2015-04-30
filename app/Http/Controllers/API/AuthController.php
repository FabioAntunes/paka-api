<?php namespace App\Http\Controllers\API;

use Tokenizer;
use App\Http\Requests\AuthRequest;
use App\Paka\Transformers\TokensTransformer;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends ApiController {

    /**
     * @var TokensTransformer
     */
    protected $tokensTransformer;


    public function __construct()
    {
//        $this->middleware('jwt.auth', ['except' => ['login']]);
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

        try
        {
            if (!$token = JWTAuth::attempt($credentials))
            {

                return $this->setStatusCode(401)->respondWithError('Invalid credentials');
            }
        } catch (JWTException $e)
        {
            return $this->setStatusCode(500)->respondWithError('Could not create token');
        }

        return $this->respond($token);
    }

    public function refreshToken()
    {
        try
        {
            JWTAuth::parseToken();
            return $this->respond(JWTAuth::refresh());
        } catch (JWTException $e)
        {
            return $this->setStatusCode(500)->respondWithError('Could not create token');
        }

    }
}