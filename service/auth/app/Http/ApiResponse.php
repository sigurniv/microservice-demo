<?php

namespace App\Http;


use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse implements Responsable
{
    protected $data = [];
    /** @var ApiResponseError */
    protected $error;
    protected $statusCode = 200;

    protected $headers = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return ApiResponse
     */
    public function setData(array $data): ApiResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getError(): ApiResponseError
    {
        return $this->error;
    }

    /**
     * @param array $error
     * @return ApiResponse
     */
    public function setError(ApiResponseError $error): ApiResponse
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        if (!empty($this->error)) {
            return $this->error->errorCode;
        }

        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return ApiResponse
     */
    public function setStatusCode(int $statusCode): ApiResponse
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return ApiResponse
     */
    public function setHeaders(array $headers): ApiResponse
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $content = json_encode(
            [
                'data'  => $this->getData(),
                'error' => $this->getError()
            ]
        );

        return new Response($content, $this->getStatusCode(), $this->getHeaders());
    }
}
