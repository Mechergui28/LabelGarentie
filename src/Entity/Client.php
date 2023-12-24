<?php

namespace App\Entity;

class Client
{
    private $email;
    private $civilite;
    private $nom;
    private $prenom;
    private $adresse;
    private $codePostal;
    private $ville;
    private $telephone;
    private $accepte_mail;


    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;
        return $this;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }
    public function setAccepteMail($accepte_mail){
        $this->accepte_mail = $accepte_mail;
        return $this;
    }
    public function getAccepteMail(){
        return $this->accepte_mail;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCivilite()
    {
        return $this->civilite;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getCodePostal()
    {
        return $this->codePostal;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function clientValide(){

        if($this->email == null || $this->email == ""){
            return false;
        }

        if($this->civilite == null || $this->civilite == ""){
            return false;
        }

        if($this->nom == null || $this->nom == ""){
            return false;
        }

        if($this->prenom == null || $this->prenom == ""){
            return false;
        }

        if($this->adresse == null || $this->adresse == ""){
            return false;
        }

        if($this->codePostal == null || $this->codePostal == ""){
            return false;
        }

        if($this->ville == null || $this->ville == ""){
            return false;
        }

        if($this->telephone == null || $this->telephone == ""){
            return false;
        }

        return true;

    }
    public static function initClient(array $data){

        $client=new Client();
        $client->setCivilite($data['civilite']);
        $client->setTelephone($data['telephone']);
        $client->setVille($data['ville']);
        $client->setCodePostal($data['codePostal']);
        $client->setAdresse($data['adresse']);
        $client->setNom($data['nom']);
        $client->setPrenom($data['prenom']);
        $client->setEmail($data['email']);
        $client->setAccepteMail($data['accepte_mail']);


        return $client;
    }
    public static function loginClient(array $data){
        $client=new Client();
        $client->setCivilite(isset($data['civilite']) ? $data['civilite'] : '');
        $client->setTelephone(isset($data['telephone']) ? $data['telephone'] : '');
        $client->setVille(isset($data['ville']) ? $data['ville'] : '');
        $client->setCodePostal(isset($data['codePostal']) ? $data['codePostal'] : 0);
        $client->setAdresse(isset($data['adresse']) ? $data['adresse'] : '');
        $client->setNom(isset($data['nom']) ? $data['nom'] : '');
        $client->setPrenom(isset($data['prenom']) ? $data['prenom'] : '');
        $client->setEmail(isset($data['email']) ? $data['email'] : '');
        $client->setAccepteMail('Oui');
        return $client;
    }
  
}
