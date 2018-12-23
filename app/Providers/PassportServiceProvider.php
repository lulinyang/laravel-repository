<?php

namespace App\Providers;

use App\Auth\MuitiAuthPassportRepository;
use App\Auth\PasswordGrant;
// use League\OAuth2\Server\Grant\PasswordGrant;
use Laravel\Passport\PassportServiceProvider as BasePassportServiceProvider;
use Laravel\Passport\Passport;

class PassportServiceProvider extends BasePassportServiceProvider
{
    /**
     * Create and configure a Password grant instance.
     *
     * @return PasswordGrant
     */
    protected function makePasswordGrant()
    {
        $grant = new PasswordGrant(
            $this->app->make(MuitiAuthPassportRepository::class),
            // $this->app->make(\Laravel\Passport\Bridge\UserRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

}