<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// L'import Request n'est pas nécessaire si vous n'avez pas de route personnalisée appelant __invoke
// use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Valide et met à jour les informations de profil de l'utilisateur donné.
     *
     * @param  \App\Models\User  $user
     * @param  array<string, string>  $input
     * @return void
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            // Ajoutez ici les champs 'language_id' et 'status' si vous les envoyez aussi avec cette route
            'language_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
                'country' => $input['country'] ?? null,
                'city' => $input['city'] ?? null,
                'address' => $input['address'] ?? null,
                'language_id' => $input['language_id'] ?? 1, // Valeur par défaut si non fournie
                'status' => $input['status'] ?? 'Disponible', // Valeur par défaut
            ])->save();
        }
    }

    /**
     * Met à jour les informations de profil d'un utilisateur dont l'e-mail a été vérifié.
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'email_verified_at' => null,
            'phone' => $input['phone'] ?? null,
            'country' => $input['country'] ?? null,
            'city' => $input['city'] ?? null,
            'address' => $input['address'] ?? null,
            'language_id' => $input['language_id'] ?? 1,
            'status' => $input['status'] ?? 'Disponible',
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    /**
     * Méthode __invoke() pour gérer les appels si la route Fortify est configurée
     * pour appeler la classe comme un Invokable Controller.
     * Cette méthode va simplement déléguer à la méthode `update`.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(\Illuminate\Http\Request $request)
    {
        // 🚨 Assurez-vous que l'utilisateur est authentifié pour accéder au profil
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Appelle la méthode `update` existante avec l'utilisateur et les données de la requête
        $this->update($user, $request->all());

        return response()->json(['message' => 'Profil utilisateur mis à jour avec succès.']);
    }
}
