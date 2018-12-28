<?php
/**
 * 响应体封装
 *
 *  @author wujunzhong <1250044515@qq.com>
 * @date 2018/1/12
 */

namespace App\Traits;

use Response;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiResponse
{
    /**
     * @var int HTTP code
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * get the HTTP code
     * @return mixed
     */
    private function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set the HTTP code
     *
     * @param $statusCode
     * @return $this
     */
    private function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     *  Repond a no content response.
     *
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    /**
     * Repond a no content response.
     *
     * @return response
     */
    public function noContent()
    {
        return Response::json(null, FoundationResponse::HTTP_NO_CONTENT);
    }

    /**
     * Repond a Request format error!
     *
     * @param $message
     * @param int $code
     * @return mixed
     */
    public function failed($message = 'Request format error!', $code = FoundationResponse::HTTP_BAD_REQUEST)
    {
        return $this->setStatusCode($code)->respond(['message' => $message]);
    }

    /**
     * Repond a not found!
     *
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'not found!')
    {
        return $this->failed($message, Foundationresponse::HTTP_NOT_FOUND);
    }

    /**
     * Repond a validation error!
     *
     * @param string $message
     * @return mixed
     */
    public function formError($error = [])
    {
        return $this->setStatusCode(422)->respond(['message' => "The given data was invalid.", 'error' => $error]);
    }

    /**
     * Repond a Interface requests are too frequent!
     *
     * @param string $message
     * @return mixed
     */
    public function requestsMany($message = 'Interface requests are too frequent!')
    {
        return $this->failed($message, 429);
    }

    /**
     * Respond the error of 'Unauthorized'.
     *
     * @param  string $message
     * @return json
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->failed($message, 401);
    }

    /**
     * Respond No access
     *
     * @param string $message
     * @return mixed
     */
    public function notAccess($message = 'No access!')
    {
        return $this->failed($message, 403);
    }

    /**
     * resond a network error!
     *
     * @param string $message
     * @return mixed
     */
    public function internalError($message = "network error!")
    {
        return $this->failed($message, FoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param $data
     * @param int $code
     * @param array $header
     * @return mixed
     */
    public function success($data, $code = FoundationResponse::HTTP_CREATED, $header = [])
    {
        $data = is_string($data) ? ['message' => $data] : $data;
        return $this->setStatusCode($code)->respond($data, $header);
    }
}
