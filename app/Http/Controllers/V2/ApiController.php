<?php namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Http\Request;
use Response;

class ApiController extends Controller {

    /**
     * http status code, default is 200
     * @var integer
     */
    protected $statusCode = IlluminateResponse::HTTP_OK;
    protected $httpClient = IlluminateResponse::HTTP_OK;
    protected $database = 'paka/';
    protected $views;

    /**
     * Responds with a 404
     *
     * @param  string $message
     * @return mixed
     */
    public function respondNotFound($message = 'Not Found!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param $message
     * @return mixed
     */
    public function respondWithError($message)
    {

        return $this->respond([
            'error' => [
                'message'     => $message,
                'status_code' => $this->getStatusCode(),
            ],
        ]);
    }

    /**
     * @param $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respondWithStream($response)
    {

        return $this->respond($this->parseStream($response));
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Gets the status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code
     *
     * @param int $statusCode
     * @return $this instance
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Parse date from the request, defaults to current year and month
     * @param Request $request
     */
    public function parseDate(Request $request)
    {
        $date['month'] = intval($request->input('month', date('n')));
        $date['year'] = intval($request->input('year', date('Y')));

        return $date;
    }
}