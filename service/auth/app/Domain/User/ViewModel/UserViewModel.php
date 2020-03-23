<?php

namespace App\Domain\User\ViewModel;


use App\Domain\User\Model\User;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class UserViewModel implements Responsable
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return new Response(
            json_encode([
                'id'    => (int)$this->user->id,
                'email' => (string)$this->user->email,
            ])
        );
    }
}
