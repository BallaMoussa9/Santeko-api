<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ❌ Ne PAS désactiver les routes si tu veux utiliser /login
        // Fortify::ignoreRoutes();

        // Enregistrement des actions Fortify
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 🔐 Authentification personnalisée avec vérification du rôle
        Fortify::authenticateUsing(function(Request $request) {
            $role = Role::where('name', $request->role)->first();

            if (! $role) return null;

            $user = User::where('email', $request->email)
                        ->where('role_id', $role->id)
                        ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user; // ✅ on retourne juste l'utilisateur ici
            }

            return null;
        });

        // Si tu veux désactiver les vues par défaut (utile pour API uniquement)
        Fortify::loginView(fn() => abort(404));
        Fortify::registerView(fn() => abort(404));
    }
}
