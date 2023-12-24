<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{

    private $httpClient;
    private $param;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $param)
    {
        $this->httpClient = $httpClient;
        $this->param = $param;
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @throws UserNotFoundException if the user is not found
     */
    public function loadUserByIdentifier($identifier): UserInterface
    {

        $user = new User();

        $response = $this->httpClient->request(
            'GET',
            $this->param->get('app.wsdv_url') . 'espace/compte/' . urlencode($identifier) . '',
            [
                'auth_basic' => [$this->param->get('app.wsdv_login'), $this->param->get('app.wsdv_password')],
            ]
        );

        if($response->getStatusCode() === 200) {

            $dataWS = $response->toArray();

            $user->setId($dataWS["compte"]["id"]);
            $user->setCivilite($dataWS["compte"]["civilite"]);
            $user->setNom($dataWS["compte"]["nom"]);
            $user->setPrenom($dataWS["compte"]["prenom"]);
            $user->setAdresse($dataWS["compte"]["adresse"]);
            $user->setVille($dataWS["compte"]["ville"]);
            $user->setCodePostal($dataWS["compte"]["codePostal"]);
            $user->setTelephone($dataWS["compte"]["telephone"]);
            $user->setEmail($dataWS["compte"]["mail"]);
            $user->setPassword($dataWS["compte"]["password"]);
            $user->setAccepteMail($dataWS["compte"]["accepte_mail"] == "Oui" ? true : false);

        }else{

            $user->setPassword('');

        }

        return $user;
    }

    /**
     * @deprecated since Symfony 5.3, loadUserByIdentifier() is used instead
     */
    public function loadUserByUsername($username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $user;

        // Return a User object after making sure its data is "fresh".
        // Or throw a UsernameNotFoundException if the user no longer exists.
        throw new \Exception('TODO: fill in refreshUser() inside '.__FILE__);
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    /**
     * Upgrades the hashed password of a user, typically for using a better hash algorithm.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO: when hashed passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newHashedPassword);
    }
}
