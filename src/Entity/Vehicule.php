<?php

namespace App\Entity;

class Vehicule
{
    private $marque;
    private $modele;
    private $immatriculation;
    private $kilometrage;
    private $prix;
    private $energie;
    private $date;
    private $serie;
    private $mine;

    public function setMarque($marque)
    {
        $this->marque = $marque;
        return $this;
    }

    public function setModele($modele)
    {
        $this->modele = $modele;
        return $this;
    }

    public function setImmatriculation($immatriculation)
    {
        $this->immatriculation = $immatriculation;
        return $this;
    }

    public function setKilometrage($kilometrage)
    {
        $this->kilometrage = $kilometrage;
        return $this;
    }

    public function setPrix($prix)
    {
        $this->prix = $prix;
        return $this;
    }

    public function setEnergie($energie)
    {
        $this->energie = $energie;
        return $this;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    public function setMine($mine)
    {
        $this->mine = $mine;
        return $this;
    }

    public function getMarque()
    {
        return $this->marque;
    }

    public function getModele()
    {
        return $this->modele;
    }

    public function getImmatriculation()
    {
        return $this->immatriculation;
    }

    public function getKilometrage()
    {
        return $this->kilometrage;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function getEnergie()
    {
        return $this->energie;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function getMine()
    {
        return $this->mine;
    }

    public function vehiculeValide(){

        if($this->marque == null || $this->marque == ""){
            return false;
        }

        if($this->modele == null || $this->modele == ""){
            return false;
        }

        if($this->immatriculation == null || $this->immatriculation == ""){
            return false;
        }

        if($this->kilometrage == null || $this->kilometrage == ""){
            return false;
        }

        if($this->prix == null || $this->prix == ""){
            return false;
        }

        if($this->energie == null || $this->energie == ""){
            return false;
        }

        if($this->date == null || $this->date == ""){
            return false;
        }

        return true;

    }
    public static function getVehicule( array $row ) {
        $v=new Vehicule();
        $v->setDate($row["date"]);
        $v->setEnergie($row["energie"]);
        $v->setImmatriculation($row["immatriculation"]);
        $v->setKilometrage($row["kilometrage"]);
        $v->setMarque($row["marque"]);
        $v->setModele($row["modele"]);
        $v->setPrix($row["prix"]);
        $v->setMine( (isset($row["mine"])) ? $row["mine"] : '' );
        $v->setSerie( (isset($row["serie"])) ? $row["serie"] : '' );
        return $v;
    }

}
