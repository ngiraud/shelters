<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\SecuritySchemes\OAuthFlow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::shouldBeStrict();

        $this->bootPassport();

        $this->bootScramble();
    }

    protected function bootScramble(): void
    {
        Gate::define('viewApiDocs', function (?User $user) {
            return true;
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::oauth2()
                              ->flow('password', function (OAuthFlow $flow) {
                                  $flow
                                      ->tokenUrl(route('passport.token'))
                                      ->refreshUrl(route('passport.token.refresh'));
                              })
                              ->flow('implicit', function (OAuthFlow $flow) {
                                  $flow
                                      ->authorizationUrl(route('passport.authorizations.authorize'))
                                      ->addScope('write:pets', 'modify pets in your account');
                              })
            );
        });
    }

    protected function bootPassport(): void
    {
        Passport::enablePasswordGrant();
    }
}
