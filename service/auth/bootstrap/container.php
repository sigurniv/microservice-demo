<?php

$app->singleton(
    \App\Domain\User\Repository\IUserRepository::class,
    \App\Domain\User\Repository\EloquentUserRepository::class
);
$app->singleton(
    \App\Domain\User\Repository\IUserAuthRepository::class,
    \App\Domain\User\Repository\EloquentUserAuthRepository::class
);
$app->singleton(
    \App\Domain\Auth\Support\JWT\ITokenGenerator::class,
    \App\Domain\Auth\Support\JWT\JWTTokenGenerator::class
);

$app->bind(\App\Http\Response\IResponder::class, \App\Http\Response\Responder::class);
