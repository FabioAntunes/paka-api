<?php namespace App\Http\Controllers\V2;

use App\Http\Requests\AuthRequest;
use CouchDB;


class AuthController extends ApiController {

    /**
     * Authenticates a user, if succeed returns his token, else returns a 401
     *
     * @param AuthRequest $request
     * @return \Response
     */
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $jar = new \GuzzleHttp\Cookie\CookieJar;
        $response = CouchDB::execute($request->method(),'_session', [
            'form_params' => [
                'name'     => 'email_'.$credentials['email'],
                'password' => $credentials['password']
            ],
            'cookies' => $jar
        ]);

        $user = $this->parseStream($response);

        $customClaims = ['name' => $credentials['email'], 'token' => $jar->toArray()[0]['Value']];

        $payload = \JWTFactory::make($customClaims);

        $token = \JWTAuth::encode($payload);
        $user->token = $token->get();
        return $this->respond($user);
    }


    public function logout()
    {

        $response = CouchDB::executeAuth(\Request::method(),'_session');

        $cookie = \Cookie::forget('GuzzleCookie');

        return $this->respond($response->getReasonPhrase())->withCookie($cookie);
    }
}