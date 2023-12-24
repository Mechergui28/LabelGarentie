<?php

namespace App\Entity;

class Signataire
{

    private $nom;
    private $prenom;
    private $email;
    private $langue;

    public function setPrenom($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getPrenom(){
        return $this->firstname;
    }

    public function setNom($lastname){
        $this->lastname = $lastname;
        return $this;
    }

    public function getNom(){
        return $this->lastname;
    }

    public function setEmail($email){
        $this->email = $email;
        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setLangue($langue){
        $this->langue = $langue;
        return $this;
    }

    public function getLangue(){
        return $this->langue;
    }

}