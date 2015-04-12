<?php namespace App\Http\Middleware;

use Closure;
use Tokenizer;

class VerifyPakaToken {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY', false);

        if ($apiKey && Tokenizer::authenticate($apiKey))
        {
            return $next($request);
        } else
        {
            return response('Unauthorized.', 401);
        }

    }

}
