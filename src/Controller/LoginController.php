<?php

namespace App\Controller;

use App\Security\LoginFormAuthenticator;
use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login',methods: ['POST'])]
    public function index(AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator,Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
       return $this->redirectToRoute('app_info_client');
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
//        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
