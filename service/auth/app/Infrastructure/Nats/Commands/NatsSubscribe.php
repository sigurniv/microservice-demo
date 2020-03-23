<?php

namespace App\Infrastructure\Nats\Commands;

use App\Domain\Auth\Action\GetTokenAction;
use App\Domain\User\DTO\UserData;
use App\Domain\User\ViewModel\UserAuthViewModel;
use App\Infrastructure\Nats\Nats;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nats\Message;

class NatsSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nats:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to nats';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Nats $nats
     * @param GetTokenAction $getTokenAction
     * @return mixed
     */
    public function handle(Nats $nats, GetTokenAction $getTokenAction)
    {
        $nats->getClient()->subscribe(
            'auth.token',
            function ($message) use ($getTokenAction) {
                /** @var  Message $message */
                try {
                    $request  = new Request(json_decode($message->getBody(), true));
                    $userAuth = $getTokenAction->handle(UserData::fromRequest($request));
                    $result   = new UserAuthViewModel($userAuth);
                    $message->reply($result->toResponse($request)->getContent());
                } catch (\Exception $e) {
                    $message->reply(json_encode(['error' => $e->getMessage()]));
                }

            }
        );

        Log::info('Subscribed to auth.token');

        $nats->getClient()->wait();

        Log::info('Stopped waiting for nats');
    }
}
