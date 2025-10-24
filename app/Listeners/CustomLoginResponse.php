<?php

namespace App\Http\Responses;

use App\Contracts\LoginResponse;
use Illuminate\Http\Request;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        $user = $request->user();
        $user->load('roles');

        return response()->json([
            'status' => 'success',
            'message' => 'Connexion réussie',
            'user' => $user,
            'role' => $user->role->name ?? null,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }
}
