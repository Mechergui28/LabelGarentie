<?php

namespace App\Entity;

class Prelevement
{
    private $iban;
    private $bic;
    private $nomPrenom;
    private $adresse;
    private $cp;
    private $ville;

    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }

    public function setBic($bic)
    {
        $this->bic = $bic;
        return $this;
    }

    public function setNomPrenom($nomPrenom)
    {
        $this->nomPrenom = $nomPrenom;
        return $this;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function setCp($cp)
    {
        $this->cp = $cp;
        return $this;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function getBic()
    {
        return $this->bic;
    }

    public function getNomPrenom()
    {
        return $this->nomPrenom;
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getCp()
    {
        return $this->cp;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function prelevementValide(){
        if($this->iban == null || $this->iban == ""){
            return false;
        }

        if($this->bic == null || $this->bic == ""){
            return false;
        }

        if($this->nomPrenom == null || $this->nomPrenom == ""){
            return false;
        }

        if($this->adresse == null || $this->adresse == ""){
            return false;
        }

        if($this->cp == null || $this->cp == ""){
            return false;
        }

        if($this->ville == null || $this->ville == ""){
            return false;
        }

        return true;
    }
    public static function initPrelevement(array $data){

        $prelevement=new Prelevement();
        $prelevement->setAdresse($data['adresse']);
        $prelevement->setVille($data['ville']);
        $prelevement->setBic($data['bic']);
        $prelevement->setIban($data['iban']);
        $prelevement->setNomPrenom($data['nomPrenom']);
        return $prelevement;
    }
}
