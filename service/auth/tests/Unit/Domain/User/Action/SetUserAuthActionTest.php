<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\User\Action\SetUserAuthAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mockery;
use Tests\MockeryDefaultTestCase;

class SetUserAuthActionTest extends MockeryDefaultTestCase
{
    /** @var Mockery\MockInterface */
    protected $userAuthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAuthRepository = Mockery::mock(IUserAuthRepository::class);
    }

    public function testHandleAddsUserAuth()
    {
        $user     = new User();
        $userAuth = new UserAuth();

        $this->givenUserAuthRepositoryReturnsAuths($user, collect($userAuth));
        $this->givenUserAuthRepositoryDoesNotDeleteAuths($user);
        $this->givenUserAuthRepositoryAddsAuth($user, $userAuth, $userAuth);

        $action = $this->getFindUserAction();
        $result = $action->handle($user, $userAuth);

        $this->assertEquals($userAuth, $result);
    }

    public function testHandleReturnsNullIfFailsToAdd()
    {
        $user     = new User();
        $userAuth = new UserAuth();

        $this->givenUserAuthRepositoryReturnsAuths($user, collect($userAuth));
        $this->givenUserAuthRepositoryAddsAuth($user, $userAuth, null);

        $action = $this->getFindUserAction();
        $result = $action->handle($user, $userAuth);

        $this->assertNull($result);
    }

    public function testHandleDeletesAuths()
    {
        $user     = new User();
        $userAuth = new UserAuth();

        $this->givenUserAuthRepositoryReturnsAuths($user, collect(array_fill(0, 11, $userAuth)));
        $this->givenUserAuthRepositoryDeletesAuths($user);
        $this->givenUserAuthRepositoryAddsAuth($user, $userAuth, $userAuth);

        $action = $this->getFindUserAction();
        $result = $action->handle($user, $userAuth);

        $this->assertEquals($userAuth, $result);
    }

    protected function givenUserAuthRepositoryReturnsAuths(User $user, Collection $userAuths)
    {
        $this->userAuthRepository
            ->shouldReceive('getUserAuths')
            ->with($user)
            ->andReturn($userAuths);
    }

    protected function givenUserAuthRepositoryAddsAuth(User $user, UserAuth $userAuth, ?UserAuth $returnUserAuth = null)
    {
        $this->userAuthRepository
            ->shouldReceive('addUserAuth')
            ->with($user, $userAuth)
            ->andReturn($returnUserAuth);
    }

    protected function givenUserAuthRepositoryDeletesAuths(User $user)
    {
        $this->userAuthRepository
            ->shouldReceive('deleteAuths')
            ->with($user);
    }

    protected function givenUserAuthRepositoryDoesNotDeleteAuths(User $user)
    {
        $this->userAuthRepository
            ->shouldNotReceive('deleteAuths')
            ->with($user);
    }


    protected function getFindUserAction(): SetUserAuthAction
    {
        return new SetUserAuthAction($this->userAuthRepository);
    }

}
