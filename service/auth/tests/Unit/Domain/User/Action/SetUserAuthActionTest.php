<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\User\Action\SetUserAuthAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Infrastructure\Exception\InternalErrorException;
use Illuminate\Support\Collection;
use Tests\fakes\Domain\User\Repository\FakeUserAuthRepository;
use Tests\MockeryDefaultTestCase;

class SetUserAuthActionTest extends MockeryDefaultTestCase
{
    /** @var FakeUserAuthRepository */
    protected $userAuthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAuthRepository = new FakeUserAuthRepository();
    }

    /**
     * @throws \App\Infrastructure\Exception\InternalErrorException
     */
    public function testHandleAddsUserAuth()
    {
        $user     = new User();
        $userAuth = new UserAuth();

        $this->givenUserAuthRepositoryReturnsAuths(collect($userAuth));
        $this->givenUserAuthRepositoryDoesNotDeleteAuths($user);
        $this->givenUserAuthRepositoryAddsAuth($userAuth);

        $action = $this->getSetUserAuthAction();
        $result = $action->handle($user, $userAuth);

        $this->assertEquals($userAuth, $result);
    }

    /**
     * @throws InternalErrorException
     */
    public function testHandleReturnsNullIfFailsToAdd()
    {
        $this->expectException(InternalErrorException::class);

        $userId   = 'userId';
        $user     = new User();
        $user->id = $userId;
        $userAuth = new UserAuth(['user_id' => $userId]);

        $this->givenUserAuthRepositoryReturnsAuths(new Collection([new UserAuth()]));
        $this->userAuthRepository->failsToAddAuth();

        $action = $this->getSetUserAuthAction();
        $result = $action->handle($user, $userAuth);

        $this->assertNull($result);
    }

    /**
     * @throws InternalErrorException
     */
    public function testHandleDeletesAuths()
    {
        $user     = new User();
        $userAuth = new UserAuth();

        $this->givenUserAuthRepositoryReturnsAuths(collect(array_fill(0, 11, $userAuth)));
        $this->givenUserAuthRepositoryDeletesAuths($user);
        $this->givenUserAuthRepositoryAddsAuth($userAuth);

        $action = $this->getSetUserAuthAction();
        $result = $action->handle($user, $userAuth);

        $this->assertEquals($userAuth, $result);
    }

    protected function givenUserAuthRepositoryReturnsAuths(Collection $userAuths)
    {
        foreach ($userAuths as $userAuth) {
            $this->userAuthRepository->addUserAuth($userAuth);
        }
    }

    protected function givenUserAuthRepositoryAddsAuth(UserAuth $returnUserAuth)
    {
        $this->userAuthRepository->addUserAuth($returnUserAuth);
    }

    protected function givenUserAuthRepositoryDeletesAuths(User $user)
    {
        $this->userAuthRepository->deleteAuths($user);
    }

    protected function givenUserAuthRepositoryDoesNotDeleteAuths(User $user)
    {
        return $this->userAuthRepository->getUserAuths($user)->count() > 0;
    }


    protected function getSetUserAuthAction(): SetUserAuthAction
    {
        return new SetUserAuthAction($this->userAuthRepository);
    }

}
