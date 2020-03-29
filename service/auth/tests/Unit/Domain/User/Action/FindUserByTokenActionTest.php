<?php

namespace Tests\Unit\Domain\User\Action;


use App\Domain\Auth\DTO\TokenDataDto;
use App\Domain\User\Action\FindUserByTokenAction;
use App\Domain\User\Model\User;
use App\Domain\User\Model\UserAuth;
use App\Domain\User\Repository\IUserAuthRepository;
use Carbon\CarbonImmutable;
use Mockery;
use Tests\MockeryDefaultTestCase;

class FindUserByTokenActionTest extends MockeryDefaultTestCase
{

    /** @var Mockery\MockInterface */
    protected $userAuthRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userAuthRepository = Mockery::mock(IUserAuthRepository::class);
    }

    public function testHandleReturnsUser()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = new UserAuth();
        $userAuth->token_expires_at = (new CarbonImmutable())->addDay();
        $user                       = new User([
            'email' => 'test@gmail.com'
        ]);
        $userAuth->user             = $user;

        $this->givenUserAuthRepositoryReturnsUserAuth($tokenData, $userAuth);
        $action = $this->getFindUserAction();

        $result = $action->handle($tokenData);
        $this->assertEquals($user, $result);
    }

    public function testHandleThrowsExceptionIfAuthNotFound()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = null;

        $this->givenUserAuthRepositoryReturnsUserAuth($tokenData, $userAuth);
        $action = $this->getFindUserAction();

        $this->expectException(\Exception::class);
        $result = $action->handle($tokenData);
    }

    public function testHandleThrowsExceptionIfAuthIsExpired()
    {
        $token                      = 'token';
        $tokenData                  = new TokenDataDto($token);
        $userAuth                   = null;
        $userAuth                   = new UserAuth();
        $userAuth->token_expires_at = (new CarbonImmutable())->subHour();

        $this->givenUserAuthRepositoryReturnsUserAuth($tokenData, $userAuth);
        $action = $this->getFindUserAction();

        $this->expectException(\Exception::class);
        $result = $action->handle($tokenData);
    }

    protected function givenUserAuthRepositoryReturnsUserAuth(TokenDataDto $tokenData, ?UserAuth $userAuth)
    {
        $this->userAuthRepository
            ->shouldReceive('findByToken')
            ->with($tokenData->token)
            ->andReturn($userAuth);
    }

    protected function getFindUserAction(): FindUserByTokenAction
    {
        return new FindUserByTokenAction($this->userAuthRepository);
    }

}
