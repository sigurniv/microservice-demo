<?php

namespace App\Domain\User\ViewModel;


use App\Domain\User\Model\UserAuth;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class UserAuthViewModel implements Responsable
{
    protected $userAuth;

    public function __construct(UserAuth $userAuth)
    {
        $this->userAuth = $userAuth;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return new Response(json_encode($this->userAuth->toArray()));
    }
}
