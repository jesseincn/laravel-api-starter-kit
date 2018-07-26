<?php

namespace App\Http\Controllers\Api\v1\Traits;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponse
{
    /**
     * @var int
     */
    protected $statusCode = FoundationResponse::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return mixed
     */
    public function respond($data, $header = [])
    {
        return Response::json($data, $this->getStatusCode(), $header);
    }

    /**
     * @param $status
     * @param array $data
     * @param null $code
     * @return mixed
     */
    public function status($status, array $data, $code = null)
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        $data = array_merge($status, $data);
        return $this->respond($data);
    }

    /**
     * @param $message
     * @param int $code
     * @param string $status
     * @return mixed
     */
    public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error')
    {

        return $this->status($status, [
            'message' => $message
        ], $code);
    }

    /**
     * @param $message
     * @param string $status
     * @return mixed
     */
    public function message($message, $status = 'success')
    {
        return $this->status($status, [
            'message' => $message
        ]);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function internalError($message = 'Internal Error!')
    {
        return $this->setStatusCode(FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->failed($message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function created($message = 'created')
    {
        return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
            ->message($message);
    }

    /**
     * @param $data
     * @param string $status
     * @return mixed
     */
    public function success($data, $status = 'success')
    {
        return $this->status($status, compact('data'));
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function notFond($message = 'Not Fond!')
    {
        return $this->setStatusCode(FoundationResponse::HTTP_NOT_FOUND)->failed($message);
    }

    /**
     * 含有分页的资源返回方法
     *
     * @param AnonymousResourceCollection $collection
     * @param string $status
     * @param null $code
     * @return mixed
     */
    public function respondForPaginate(AnonymousResourceCollection $collection, $status = 'success', $code = null)
    {
        if ($code) {
            $this->setStatusCode($code);
        }

        $status = [
            'status' => $status,
            'code' => $this->statusCode
        ];

        return $collection->additional($status);
    }
}