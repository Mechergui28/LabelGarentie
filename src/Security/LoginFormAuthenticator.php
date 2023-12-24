<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator {

    private $router;

    public function __construct(RouterInterface $router){
        $this->router = $router;
    }

    public function authenticate(Request $request) : Passport{

        $email = $request->request->get('_username');
        $plaintextPassword = $request->request->get('_password');

        return new Passport(new UserBadge($email), new PasswordCredentials($plaintextPassword));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token,  string $firewallName) : Response {
        return new Response('ok');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response('Erreur', Response::HTTP_UNAUTHORIZED);
    }

    protected function getLoginUrl(Request $request) : string
    {
        //En cas d'échec de la connexion, l'utilisateur est renvoyé sur cette page
        return $this->router->generate('app_login');
    }

}