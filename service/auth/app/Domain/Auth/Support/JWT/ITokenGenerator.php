<?php

namespace App\Domain\Auth\Support\JWT;


use App\Domain\Auth\Model\Token;
use Carbon\CarbonImmutable;

interface ITokenGenerator
{
    public function generate(array $payload, CarbonImmutable $expirationDate): Token;
}
