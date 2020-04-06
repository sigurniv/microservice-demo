<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\User\Action\FindUserByEmailAction;
use App\Domain\User\DTO\UserDataDto;
use App\Domain\User\Repository\IUserRepository;
use App\Domain\User\Model\User;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\fakes\Domain\User\Repository\FakeUserRepository;
use Tests\MockeryDefaultTestCase;

class FindUserByEmailActionTest extends MockeryDefaultTestCase
{
    /** @var FakeUserRepository */
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new FakeUserRepository();
    }

    /**
     * @throws \App\Http\Exception\ValidationException
     */
    public function testHandleChecksPassword()
    {
        $email    = 'test@gmail.com';
        $password = 'password';
        $user     = new User(['email' => $email, 'password' => Hash::make('213')]);
        $this->givenUserRepositoryFindByEmailReturnsUser($user);

        $userData = new UserDataDto($email, $password);
        $action = $this->getFindUserAction();
        $result = $action->handle($userData);

        $this->assertNull($result);
    }

    /**
     * @throws \App\Http\Exception\ValidationException
     */
    public function testHandleReturnsUser()
    {
        $email    = 'test@gmail.com';
        $password = 'password';
        $user     = new User(['email' => $email, 'password' => Hash::make($password)]);
        $this->givenUserRepositoryFindByEmailReturnsUser($user);

        $userData = new UserDataDto($email, $password);
        $action = $this->getFindUserAction();
        $result = $action->handle($userData);

        $this->assertEquals($user, $result);
    }

    protected function givenUserRepositoryFindByEmailReturnsUser(User $user)
    {
       $this->userRepository->save($user);
    }

    protected function getFindUserAction(): FindUserByEmailAction
    {
        return new FindUserByEmailAction($this->userRepository);
    }
}
