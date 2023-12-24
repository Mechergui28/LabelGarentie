<?php

namespace App\Entity;

class Contrat
{
    private $vehicule;
    private $garantie;
    private $client;
    private $prelevement;
    private $code;
    private $libelle;
    private $numeroContrat;
    private $typePaiement;
    private $moyenPaiement;

    public function __construct()
    {
        $this->vehicule = new Vehicule();
        $this->garantie = new Garantie();
        $this->client = new Client();
        $this->prelevement = new Prelevement();
    }
    public function setVehicule(Vehicule $vehicule)
    {
        $this->vehicule = $vehicule;
        return $this;
    }
    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }
    public function setGarantie($garantie){
        $this->garantie=$garantie;
        return $this;
    }

    public function setPrelevement($prelevement)
    {
        $this->prelevement = $prelevement;
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function setNumeroContrat($numeroContrat)
    {
        $this->numeroContrat = $numeroContrat;
        return $this;
    }

    public function setTypePaiement($typePaiement)
    {
        $this->typePaiement = $typePaiement;
        return $this;
    }

    public function setMoyenPaiement($moyenPaiement)
    {
        $this->moyenPaiement = $moyenPaiement;
        return $this;
    }
 
    public function getVehicule(){
        return $this->vehicule;
    }  

    public function getGarantie(){
        return $this->garantie;
    }

    public function getClient(){
        return $this->client;
    }

    public function getPrelevement(){
        return $this->prelevement;
    }

    public function getCode(){
        return $this->code;
    }

    public function getLibelle(){
        return $this->libelle;
    }

    public function getNumeroContrat(){
        return $this->numeroContrat;
    }

    public function getTypePaiement(){
        return $this->typePaiement;
    }

    public function getMoyenPaiement()
    {
        return $this->moyenPaiement;
    }
    
}
