<?php

namespace App\Domain\User\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class User
 * @property string $email
 * @property int $id
 * @property string $password
 * @property Collection $auths
 * @package App\Domain\User\Model
 */
class User extends Model
{
    protected $fillable = [
        'email', 'password', 'name'
    ];

    public function auths()
    {
        return $this->hasMany(UserAuth::class);
    }


}
