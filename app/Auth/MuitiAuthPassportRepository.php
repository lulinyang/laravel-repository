<?php

namespace App\Auth;

use App;
use Illuminate\Http\Request;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Bridge\User;
use RuntimeException;

class MuitiAuthPassportRepository extends UserRepository
{

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {   
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            if (method_exists($model, 'findForPassportMulti')) {
                $user = (new $model)->findForPassportMulti(App::make(Request::class)->all());
            }else{
                $user = (new $model)->where('name', $username)->first();
            }
        }

        if (! $user) {
            return;
        } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (! $user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } elseif (! $this->hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }


}