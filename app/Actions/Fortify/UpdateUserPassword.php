<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use Illuminate\Http\Request;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Valide et met à jour le mot de passe de l'utilisateur.
     *
     * @param  \App\Models\User  $user
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        // --- NOUVEAU DIAGNOSTIC ---
        \Log::info('Tentative de mise à jour de mot de passe pour l\'utilisateur ID: ' . $user->id);
        \Log::info('Mot de passe actuel fourni: ' . $input['current_password']);
        \Log::info('Mot de passe hashé en BDD: ' . $user->password);
        \Log::info('Vérification Hash::check(): ' . (Hash::check($input['current_password'], $user->password) ? 'TRUE' : 'FALSE'));

        // Vous pouvez aussi utiliser dd() si vous voulez arrêter l'exécution pour inspecter
        // dd([
        //     'user_id' => $user->id,
        //     'current_password_input' => $input['current_password'],
        //     'hashed_password_in_db' => $user->password,
        //     'hash_check_result' => Hash::check($input['current_password'], $user->password),
        // ]);
        // --- FIN DIAGNOSTIC ---


        Validator::make($input, [
            // Utilisez 'web' si vous vous authentifiez avec les sessions Laravel classiques
            // Utilisez 'sanctum' si vous vous authentifiez uniquement via des tokens Sanctum
            'current_password' => ['required', 'string', 'current_password:sanctum'], // 👈 POTENTIEL CHANGEMENT ICI
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        $this->update($user, $request->all());

        return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }
}
