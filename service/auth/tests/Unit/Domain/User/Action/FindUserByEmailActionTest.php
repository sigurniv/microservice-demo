<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\User\Action\FindUserByEmailAction;
use App\Domain\User\DTO\UserDataDto;
use App\Domain\User\Repository\IUserRepository;
use App\Domain\User\Model\User;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\MockeryDefaultTestCase;

class FindUserByEmailActionTest extends MockeryDefaultTestCase
{
    /** @var Mockery\MockInterface */
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(IUserRepository::class);
    }

    public function testHandleChecksPassword()
    {
        $email    = 'test@gmail.com';
        $password = 'password';
        $user     = new User(['email' => 'email', 'password' => Hash::make('213')]);
        $this->givenUserRepositoryFindByEmailReturnsUser($email, $user);

        $userData = new UserDataDto($email, $password);
        $action = $this->getFindUserAction();
        $result = $action->handle($userData);

        $this->assertNull($result);
    }

    public function testHandleReturnsUser()
    {
        $email    = 'test@gmail.com';
        $password = 'password';
        $user     = new User(['email' => 'test', 'password' => Hash::make($password)]);
        $this->givenUserRepositoryFindByEmailReturnsUser($email, $user);

        $userData = new UserDataDto($email, $password);
        $action = $this->getFindUserAction();
        $result = $action->handle($userData);

        $this->assertEquals($user, $result);
    }

    protected function givenUserRepositoryFindByEmailReturnsUser(string $email, User $user)
    {
        $this->userRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->andReturn($user);
    }

    protected function getFindUserAction(): FindUserByEmailAction
    {
        return new FindUserByEmailAction($this->userRepository);
    }
}
