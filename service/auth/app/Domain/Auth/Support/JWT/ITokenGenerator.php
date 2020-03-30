<?php

namespace App\Domain\Auth\Support\JWT;


use App\Domain\Auth\VO\Token;
use Carbon\CarbonImmutable;

interface ITokenGenerator
{
    const TOKEN_LIFETIME_SECONDS         = 60 * 60 * 1;
    const REFRESH_TOKEN_LIFETIME_SECONDS = 60 * 60 * 24;

    public function generate(array $payload, CarbonImmutable $expirationDate): Token;
}
