<?php

namespace App\Entity;

use App\Security\User;

class UserCreation
{
    private $nom;
    private $prenom;
    private $civilite;
    private $adresse;
    private $codePostal;
    private $ville;
    private $telephone;
    private $mail;
    private $pwd;
    private $accepte_mail;
    private $CGU;

    public function __construct()
    {
    }
    /**
     * @return mixed
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * @param mixed $civilite
     */
    public function setCivilite($civilite): void
    {
        $this->civilite = $civilite;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param mixed $codePostal
     */
    public function setCodePostal($codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville): void
    {
        $this->ville = $ville;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }



    /**
     * @return mixed
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * @param mixed $pwd
     */
    public function setPwd($pwd): void
    {
        $this->pwd = $pwd;
    }
    /**
     * @return mixed
     */
    public function getAccepteMail()
    {
        return $this->accepte_mail;
    }

    /**
     * @param mixed $accepte_mail
     */
    public function setAccepteMail($accepte_mail): void
    {
        $this->accepte_mail = $accepte_mail;
    }

    /**
     * @return mixed
     */
    public function getCGU()
    {
        return $this->CGU;
    }

    /**
     * @param mixed $CGU
     */
    public function setCGU($CGU): void
    {
        $this->CGU = $CGU;
    }
    public static function initUser(User $user){
        $userCreation=new UserCreation();
        $userCreation->setCivilite($user->getCivilite());
        $userCreation->setNom($user->getNom());
        $userCreation->setPrenom($user->getPrenom());
        $userCreation->setAdresse($user->getAdresse());
        $userCreation->setCodePostal($user->getCodePostal());
        $userCreation->setVille($user->getVille());
        $userCreation->setTelephone($user->getTelephone());
        $userCreation->setAccepteMail($user->getAccepteMail());
        return $userCreation;
    }
}