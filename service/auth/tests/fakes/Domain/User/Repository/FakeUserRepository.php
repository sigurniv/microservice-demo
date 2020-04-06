<?php

namespace Tests\fakes\Domain\User\Repository;


use App\Domain\User\Model\User;
use App\Domain\User\Repository\IUserRepository;
use Illuminate\Support\Collection;

class FakeUserRepository implements IUserRepository
{
    /** @var User[] | Collection */
    protected $users;

    public function __construct()
    {
        $this->users = new Collection();
    }

    public function save(User $user): bool
    {
        $this->users[$user->email] = $user;
        return true;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }
}
