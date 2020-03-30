<?php

namespace App\Domain\Auth\VO;

final class Token
{
    private $token;
    private $expiresAt;

    public function __construct(string $token, \Carbon\CarbonImmutable $expiresAt)
    {
        $this->token     = $token;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \Carbon\CarbonImmutable
     */
    public function getExpiresAt(): \Carbon\CarbonImmutable
    {
        return $this->expiresAt;
    }
}
