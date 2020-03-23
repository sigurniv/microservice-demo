<?php

$app->bind(
    \App\Domain\User\Repository\IUserRepository::class,
    \App\Domain\User\Repository\EloquentUserRepository::class
);
$app->bind(
    \App\Domain\User\Repository\IUserAuthRepository::class,
    \App\Domain\User\Repository\EloquentUserAuthRepository::class
);
$app->bind(
    \App\Domain\Auth\Support\JWT\ITokenGenerator::class,
    \App\Domain\Auth\Support\JWT\JWTTokenGenerator::class
);
