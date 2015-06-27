<?php namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response as IlluminateResponse;
use Response;
use HttpClient;

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

    public function parseStream($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function buildUrl($view, $keys = [], $url=false){
        $url = $url ? $url : $this->database;
        $url .= $this->views[$view].'?include_docs=true';

        if(count($keys)){
            if(array_key_exists('startkey', $keys) && array_key_exists('endkey', $keys)){
                $url.='&startkey='.json_encode($keys['startkey']).'&endkey='.json_encode($keys['endkey']);
            }else if(array_key_exists('key', $keys)){
                $url.='&key='.json_encode($keys['key']);
            }
        }

        return $url;
    }

    public function buildUrlCurrentMonth($view, $keys, $url=false, $appendObject = true){

        $keys['startkey'][] = [intval(date('Y')), intval(date('n')), null];
        if($appendObject){
            $keys['endkey'][] = [intval(date('Y')), intval(date('n')), 31];
            $keys['endkey'][] = json_decode ("{}");
        }

        return $this->buildUrl($view, $keys, $url);
    }
}