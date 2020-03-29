<?php

namespace App\Api\Auth\Controllers;


use App\Domain\Auth\Action\GetTokenAction;
use App\Domain\Auth\Action\RefreshTokenAction;
use App\Domain\Auth\DTO\RefreshTokenDataDto;
use App\Domain\Auth\DTO\TokenDataDto;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\DTO\UserDataDto;
use App\Domain\User\ViewModel\UserAuthViewModel;
use App\Domain\User\ViewModel\UserViewModel;
use App\Http\ApiErrResponse;
use App\Http\Controllers\ApiController;
use App\Http\Exception\ValidationException;
use App\Infrastructure\Exception\ErrorMessageException;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    /**
     * @param Request $request
     * @param GetTokenAction $getTokenAction
     * @return UserAuthViewModel|ApiErrResponse|\Illuminate\Http\JsonResponse
     * @throws ValidationException
     * @throws ErrorMessageException
     */
    public function getToken(
        Request $request,
        GetTokenAction $getTokenAction
    )
    {
        $userAuth = $getTokenAction->handle(UserDataDto::fromRequest($request));
        return new UserAuthViewModel($userAuth);
    }

    /**
     * @param Request $request
     * @param FindUserByTokenAction $findUserByTokenAction
     * @return UserViewModel|ApiErrResponse|\Illuminate\Http\JsonResponse
     * @throws ErrorMessageException
     * @throws ValidationException
     */
    public function getUser(
        Request $request,
        FindUserByTokenAction $findUserByTokenAction
    )
    {
        $user = $findUserByTokenAction->handle(TokenDataDto::fromRequest($request));
        return new UserViewModel($user);
    }

    /**
     * @param Request $request
     * @param RefreshTokenAction $refreshTokenAction
     * @throws ErrorMessageException
     * @throws ValidationException
     */
    public function refreshToken(
        Request $request,
        RefreshTokenAction $refreshTokenAction
    )
    {
        $refreshTokenData = RefreshTokenDataDto::fromRequest($request);
        $userAuth = $refreshTokenAction->handle($refreshTokenData);
    }
}
