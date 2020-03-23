<?php

namespace App\Domain\User\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class UserAuth
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $refresh_token
 * @property Carbon $token_expires_at
 * @property Carbon $refresh_token_expires_at
 * @property User $user
 * @package App\Domain\User\Model
 */
class UserAuth extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'refresh_token',
        'token_expires_at',
        'refresh_token_expires_at'
    ];

    protected $dates = [
        'token_expires_at',
        'refresh_token_expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeWhereToken($query, string $token)
    {
        return $query->where('token', $token);
    }
}
