<?php

namespace App\Domain\User\Action;


use App\Domain\User\DTO\UserDataDto;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\IUserRepository;
use Illuminate\Support\Facades\Hash;

class FindUserByEmailAction
{
    protected $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(UserDataDto $userData): ?User
    {
        $user = $this->userRepository->findByEmail($userData->email);
        if (!$user) {
            return $user;
        }

        if (!Hash::check($userData->password, $user->password)) {
            return null;
        }

        return $user;
    }
}
