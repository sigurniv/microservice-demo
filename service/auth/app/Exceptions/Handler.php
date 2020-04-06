<?php

namespace App\Exceptions;

use App\Http\ApiErrResponse;
use App\Http\ApiResponse;
use App\Http\ApiResponseError;
use App\Http\Exception\ValidationException;
use App\Http\Response\IResponder;
use App\Http\Response\Responder;
use App\Infrastructure\Exception\ErrorMessageException;
use App\Infrastructure\Exception\InternalErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        /** @var IResponder $responder */
        $responder = resolve(Responder::class);

        switch (get_class($exception)) {
            case ValidationException::class:
                /** @var ValidationException $exception */
                $error = $exception->getValidator()->errors()->first();
                return $responder->respond(
                    (new ApiResponse())->setError(new ApiResponseError($error, 422))
                );

            case ErrorMessageException::class:
                return $responder->respond(
                    new ApiErrResponse(new ApiResponseError(trans($exception->getMessage()), $exception->getCode()))
                );

            case InternalErrorException::class:
                return $responder->respond(
                    new ApiErrResponse(new ApiResponseError('internal error', $exception->getCode()))
                );
            default:
                return parent::render($request, $exception);
        }
    }
}
