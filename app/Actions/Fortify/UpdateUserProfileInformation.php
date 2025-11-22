<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
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
        // 1. Validation : On valide seulement les champs qui pourraient être présents.
        // Si language_id n'est pas envoyé, on ne le valide pas.
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
            // Si vous envoyez language_id, vous devez vous assurer que l'ID existe
            'language_id' => ['nullable', 'integer', 'exists:languages,id'],
            'status' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        // 2. Préparation des données pour la mise à jour :
        // On ne met à jour que les champs présents dans $input, sans valeurs par défaut forcées.
        $dataToUpdate = [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            // Les autres champs sont ajoutés uniquement s'ils sont dans le tableau $input.
        ];

        // Mappage conditionnel pour les champs optionnels
        $optionalFields = ['phone', 'country', 'city', 'address', 'language_id', 'status'];
        foreach ($optionalFields as $field) {
            if (isset($input[$field])) {
                $dataToUpdate[$field] = $input[$field];
            }
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $dataToUpdate);
        } else {
            $user->forceFill($dataToUpdate)->save();
        }
    }

    /**
     * Met à jour les informations de profil d'un utilisateur dont l'e-mail a été vérifié.
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        // On ajoute le champ 'email_verified_at' => null à la mise à jour
        $input['email_verified_at'] = null;

        $user->forceFill($input)->save();

        $user->sendEmailVerificationNotification();
    }

    /**
     * Méthode __invoke() pour gérer les appels si la route Fortify est configurée
     * pour appeler la classe comme un Invokable Controller.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Appelle la méthode `update` existante avec l'utilisateur et les données de la requête
        // L'implémentation de `update` gère maintenant uniquement les champs fournis.
        $this->update($user, $request->all());

        return response()->json(['message' => 'Profil utilisateur mis à jour avec succès.']);
    }
}