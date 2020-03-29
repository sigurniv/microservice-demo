<?php

namespace App\Http\Response;

use App\Http\ApiResponse;

interface IResponder
{
    public function respond(ApiResponse $apiResponse);
}
