<?php

namespace App\Domain\Auth\Action;


use App\Domain\Auth\Support\JWT\ITokenGenerator;
use Carbon\CarbonImmutable;

class GenerateTokenAction
{
    protected $tokenGenerator;

    public function __construct(ITokenGenerator $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    public function handle(string $userId, int $seconds)
    {
        $expirationDate = (new CarbonImmutable())->addSeconds($seconds);
        $payload        = [
            "iss"    => "http://example.org",
            "iat"    => (new CarbonImmutable())->timestamp,
            "nbf"    => $expirationDate->timestamp,
            'userId' => $userId
        ];

        return $this->tokenGenerator->generate($payload, $expirationDate);
    }
}
