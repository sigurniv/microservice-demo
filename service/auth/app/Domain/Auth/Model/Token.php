<?php

namespace App\Domain\Auth\Model;


use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Token
 * @property string $token
 * @property CarbonImmutable $expires_at
 * @package App\Domain\Auth\Model
 */
class Token extends Model
{
    const TOKEN_LIFETIME_SECONDS         = 60 * 60 * 1;
    const REFRESH_TOKEN_LIFETIME_SECONDS = 60 * 60 * 24;

    protected $fillable = [
        'token', 'expires_at'
    ];
}
