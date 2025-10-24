<?php

namespace App\Contracts;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

interface LoginResponse extends LoginResponseContract
{
    // Enlève le type Request devant $request pour respecter la signature parent
    public function toResponse($request);
}
