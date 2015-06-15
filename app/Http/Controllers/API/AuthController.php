<?php namespace App\Http\Controllers\API;

use App\Paka\Transformers\UsersTransformer;
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
    protected $usesTransformers;


    public function __construct()
    {
        $this->tokensTransformer = new TokensTransformer();
        $this->usesTransformers = new UsersTransformer();

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

        $user = $this->usesTransformers->userInfo($token);
        $user['token'] = $token;
        return $this->respond($user);
    }

    public function refreshToken()
    {
        try
        {
            JWTAuth::parseToken();
            $token = JWTAuth::refresh();
            $user = $this->usesTransformers->userInfo($token);
            $user['token'] = $token;
            return $this->respond($user);
        } catch (JWTException $e)
        {
            return $this->setStatusCode(500)->respondWithError('Could not create token');
        }

    }
}