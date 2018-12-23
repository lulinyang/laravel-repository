<?php

namespace App\Auth;

use League\OAuth2\Server\Grant\PasswordGrant as BasePasswordGrant;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;



class PasswordGrant extends BasePasswordGrant
{


    /**
     * @param ServerRequestInterface $request
     * @param ClientEntityInterface  $client
     *
     * @throws OAuthServerException
     *
     * @return UserEntityInterface
     */
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $username = $this->getRequestParameter('username', $request);
        $provider = $this->getRequestParameter('provider', $request);

        if(is_null($provider) && is_null($username)){
            throw OAuthServerException::invalidRequest('username');
        }
        $password = $this->getRequestParameter('password', $request);
        if (is_null($password)) {
            throw OAuthServerException::invalidRequest('password');
        }
        $user = $this->userRepository->getUserEntityByUserCredentials(
            $username,
            $password,
            $this->getIdentifier(),
            $client
        );

        if ($user instanceof UserEntityInterface === false) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

}