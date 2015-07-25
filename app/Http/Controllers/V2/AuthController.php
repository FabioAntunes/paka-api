<?php namespace App\Http\Controllers\V2;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RecoverRequest;
use App\Http\Requests\RegisterRequest;
use App\Paka\Transformers\CategoriesTransformer;
use CouchDB;
use HttpClient;
use Mail;
use App\User;

class AuthController extends ApiController {

    protected $categoriesTransformer;

    public function __construct()
    {
        $this->categoriesTransformer = new CategoriesTransformer();
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
        return $this->respond($this->authtenticateCouch($credentials));
    }


    public function logout()
    {

        $response = CouchDB::executeAuth(\Request::method(),'_session');

        $cookie = \Cookie::forget('GuzzleCookie');

        return $this->respond($response->getReasonPhrase())->withCookie($cookie);
    }

    public function register(RegisterRequest $request){
        $credentials = $request->only('name', 'email', 'password');

        $url = '_users/org.couchdb.user:email_'.$credentials['email'];

        $response = CouchDB::executeAdmin('put', $url, [
            'json' => [
                'username' => $credentials['name'],
                'name'     => 'email_'.$credentials['email'],
                'email'     => $credentials['email'],
                'password' => $credentials['password'],
                'roles' => ['paka-user'],
                'type' => 'user'
            ]
        ]);

        $user = $this->authtenticateCouch($credentials);

        User::create([
            'username' => $credentials['name'],
            'name'     => 'email_'.$credentials['email'],
            'email' => $credentials['email'],
            'password' => bcrypt('paka'),
        ]);

        $this->categoriesTransformer->seedCategories();

        return $this->respond($user);
    }

    public function recover(RecoverRequest $request){

        Mail::raw('Text to e-mail', function($message) use ($request)
        {
            $message->from('no-reply@paka.com', 'Paka');

            $message->to($request->input('email'))->cc('bar@example.com');
        });
    }

    private function authtenticateCouch($credentials){
        $jar = new \GuzzleHttp\Cookie\CookieJar;
        $response = CouchDB::execute('post','_session', [
            'form_params' => [
                'name'     => 'email_'.$credentials['email'],
                'password' => $credentials['password']
            ],
            'cookies' => $jar
        ]);

        $user = CouchDB::parseStream($response);

        $customClaims = ['name' => $credentials['email'], 'token' => $jar->toArray()[0]['Value']];
        CouchDB::setToken($customClaims['token']);

        $payload = \JWTFactory::make($customClaims);

        $token = \JWTAuth::encode($payload);
        $user->token = $token->get();

        return $user;
    }
}