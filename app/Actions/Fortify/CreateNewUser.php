<?php
namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name'    => ['required','string','max:255'],
            'last_name'     => ['required','string','max:255'],
            'birth_date'    => ['required','date'],
            'phone'         => ['nullable','string','max:20'],
            'country'       => ['nullable','string'],
            'city'          => ['nullable','string'],
            'profile_photo' => ['nullable','string'],
            'address'       => ['nullable','string'],
            'email'         => ['required','string','email','max:255','unique:users'],
            'password'      => ['required','string','min:8','confirmed'],
        ])->validate();

        $input['password'] = Hash::make($input['password']);

        return User::create($input);
    }
}
