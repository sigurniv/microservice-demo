<?php

namespace App\Api\Auth\Controllers;


use App\Domain\Auth\Action\GetTokenAction;
use App\Domain\Auth\Action\RefreshTokenAction;
use App\Domain\Auth\DTO\RefreshTokenData;
use App\Domain\Auth\DTO\TokenData;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\DTO\UserData;
use App\Domain\User\ViewModel\UserAuthViewModel;
use App\Domain\User\ViewModel\UserViewModel;
use App\Http\ApiErrResponse;
use App\Http\ApiResponseError;
use App\Http\Controllers\ApiController;
use App\Http\Exception\ValidationException;
use App\Infrastructure\Exception\ErrorMessageException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends ApiController
{

    /**
     * @param Request $request
     * @param GetTokenAction $getTokenAction
     * @return UserAuthViewModel|ApiErrResponse|\Illuminate\Http\JsonResponse
     */
    public function getToken(
        Request $request,
        GetTokenAction $getTokenAction
    )
    {
        try {
            $userData = UserData::fromRequest($request);
        } catch (ValidationException $exception) {
            return $this->validatorError($exception->getValidator());
        }

        try {
            $userAuth = $getTokenAction->handle($userData);
        } catch (ErrorMessageException $e) {
            return new ApiErrResponse(new ApiResponseError(trans($e->getMessage()), $e->getCode()));
        }

        return new UserAuthViewModel($userAuth);
    }

    /**
     * @param Request $request
     * @param FindUserByTokenAction $findUserByTokenAction
     * @return UserViewModel|ApiErrResponse|\Illuminate\Http\JsonResponse
     */
    public function getUser(
        Request $request,
        FindUserByTokenAction $findUserByTokenAction
    )
    {
        try {
            $tokenData = TokenData::fromRequest($request);
        } catch (ValidationException $exception) {
            return $this->validatorError($exception->getValidator());
        }

        try {
            $user = $findUserByTokenAction->handle($tokenData);
        } catch (\Exception $exception) {
            return new ApiErrResponse(
                new ApiResponseError(trans('auth.failed'), Response::HTTP_UNAUTHORIZED)
            );
        }

        return new UserViewModel($user);
    }

    public function refreshToken(
        Request $request,
        RefreshTokenAction $refreshTokenAction
    )
    {
        try {
            $refreshTokenData = RefreshTokenData::fromRequest($request);
        } catch (ValidationException $exception) {
            return $this->validatorError($exception->getValidator());
        }

        $userAuth = $refreshTokenAction->handle($refreshTokenData);

    }
}
