<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Responses\CustomLoginResponse;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify; // 👈 Assurez-vous que cet import est présent

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Pour personnaliser la réponse de login
        $this->app->singleton(LoginResponseContract::class, CustomLoginResponse::class);

        // Pour manipuler le système de fichiers si besoin
        $this->app->bind('files', function () {
            return new Filesystem;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
            // 🔑 CORRECTION CLÉ : Décommenter ces lignes pour utiliser VOS actions personnalisées
            Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
            Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    }
}
