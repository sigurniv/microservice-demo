<?php

namespace App\Domain\Auth\Support\JWT;


use App\Domain\Auth\Model\Token;
use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Illuminate\Config\Repository;
use Webmozart\Assert\Assert;

class JWTTokenGenerator implements ITokenGenerator
{
    protected $key;

    public function __construct(Repository $config)
    {
        $this->key = $config->get('auth.jwt.key');
        Assert::notEmpty($this->key);
    }

    public function generate($payload, CarbonImmutable $expirationDate): Token
    {
        return new Token([
            'token'      => JWT::encode($payload, $this->key),
            'expires_at' => $expirationDate
        ]);
    }
}
