<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\Auth\DTO\TokenDataDto;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use Carbon\CarbonImmutable;
use Mockery;
use Tests\fakes\Domain\User\Repository\FakeUserAuthRepository;
use Tests\MockeryDefaultTestCase;

class FindUserByTokenActionTest extends MockeryDefaultTestCase
{
    /** @var FakeUserAuthRepository */
    protected $userAuthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAuthRepository = new FakeUserAuthRepository();
    }

    /**
     * @throws \App\Http\Exception\ValidationException
     * @throws \App\Infrastructure\Exception\ErrorMessageException
     */
    public function testHandleReturnsUser()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = new UserAuth(['token' => $token]);
        $userAuth->token_expires_at = (new CarbonImmutable())->addDay();
        $user                       = new User([
            'email' => 'test@gmail.com'
        ]);
        $userAuth->user             = $user;

        $this->givenUserAuthRepositoryReturnsUserAuth($userAuth);
        $action = $this->getFindUserAction();

        $result = $action->handle($tokenData);
        $this->assertEquals($user, $result);
    }

    /**
     * @throws \App\Http\Exception\ValidationException
     * @throws \App\Infrastructure\Exception\ErrorMessageException
     */
    public function testHandleThrowsExceptionIfAuthNotFound()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = null;

        $this->givenUserAuthRepositoryReturnsUserAuth($userAuth);
        $action = $this->getFindUserAction();

        $this->expectException(\Exception::class);
        $result = $action->handle($tokenData);
    }

    /**
     * @throws \App\Http\Exception\ValidationException
     * @throws \App\Infrastructure\Exception\ErrorMessageException
     */
    public function testHandleThrowsExceptionIfAuthIsExpired()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = new UserAuth(['token' => $token]);
        $userAuth->token_expires_at = (new CarbonImmutable())->subHour();

        $this->givenUserAuthRepositoryReturnsUserAuth($userAuth);
        $action = $this->getFindUserAction();

        $this->expectException(\Exception::class);
        $result = $action->handle($tokenData);
    }

    protected function givenUserAuthRepositoryReturnsUserAuth(?UserAuth $userAuth)
    {
        if ($userAuth) {
            $this->userAuthRepository->addUserAuth($userAuth);
        }
    }

    protected function getFindUserAction(): FindUserByTokenAction
    {
        return new FindUserByTokenAction($this->userAuthRepository);
    }

}
