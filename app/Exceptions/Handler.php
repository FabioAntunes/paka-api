<?php namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException',
		'GuzzleHttp\Exception\ClientException',
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
        if($e instanceof NotFoundHttpException)
        {
			$message = $e->getMessage() != "" ? $e->getMessage() : 'Not found';
            return Response::json($message, 404);
        }

        if ($e instanceof ClientException)
        {
            $response = $e->getResponse();
//            $cookie = \Cookie::forget('token');

//            return Response::json($response->getReasonPhrase(), $response->getStatusCode())->withCookie($cookie);
            return Response::json($response->getReasonPhrase(), $response->getStatusCode());
        }
		return parent::render($request, $e);
	}

}
