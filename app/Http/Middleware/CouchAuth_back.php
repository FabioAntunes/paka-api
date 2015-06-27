<?php namespace App\Http\Middleware;

use Closure;
use CouchDB;

class CouchAuth_back {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (CouchDB::getCookie())
        {

            $response = CouchDB::executeAuth('get', '_session');

            $user = json_decode($response->getBody());
            if ($user->userCtx->name != null)
            {
                CouchDB::setUser($user->userCtx);
                return $next($request);
            }
        }

        return response("Unauthorized", 401);

    }

}
