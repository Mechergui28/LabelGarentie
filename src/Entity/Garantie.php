<?php

namespace App\Entity;

use DateTime;

class Garantie
{
    private $code;
    private $libelle;
    private $duree;
    private $dateDebut;
    private $mt_ht;
    private $mt_ttc;
    private $mt_assu;

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

    public function setDuree($duree)
    {
        $this->duree = $duree;
        return $this;
    }

    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function setMtHT($mt_ht)
    {
        $this->mt_ht = $mt_ht;
        return $this;
    }
    

    public function setMtTTC($mt_ttc)
    {
        $this->mt_ttc = $mt_ttc;
        return $this;
    }

    public function setMtAssu($mt_assu)
    {
        $this->mt_assu = $mt_assu;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function getDuree()
    {
        return $this->duree;
    }

    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    public function getDateDebutFormat()
    {
       // $dateFormat = date_create_from_format("d/m/Y", $this->dateDebut);
        return date_format( $this->dateDebut, "Ymd");
    }

    public function getDateFin(){

        if($this->dateDebut != null && $this->dateDebut != "" && $this->duree > 0){

            $dateFin = new Datetime($this->dateDebut->format('Y-m-d H:i:s'));
            $dateFin = date_add($dateFin, date_interval_create_from_date_string($this->duree.' months'));
            $dateFin = date_sub($dateFin, date_interval_create_from_date_string('1 days'));

            return date_format($dateFin, "Ymd");

        }else{
            return null;
        }

    }

    public function getMtHT()
    {
        return $this->mt_ht;
    }

    public function getMtTTC()
    {
        return $this->mt_ttc;
    }

    public function getMtAssu()
    {
        return $this->mt_assu;
    }

    public function garantieValide(){

        if($this->code == null || $this->code == ""){
            return false;
        }

        if($this->libelle == null || $this->libelle == ""){
            return false;
        }

        if($this->duree == null || $this->duree <= 0){
            return false;
        }

        //Pas de vérif
        // if($this->dateDebut == null) {
        //     dd('datedebut');
        //     return false;
        // }

        if ($this->mt_ht == null){
            return false;
        }

        if($this->mt_ttc == null){
            return false;
        }

        return true;

    }
    public static function setGarantie(array $row){
        $garantie=new Garantie();
        $garantie->setCode($row["ART_CODE"]);
        $garantie->setLibelle($row["ART_LIBELLE"]);
        $garantie->setDuree($row["ART_DUREE"]);
        $garantie->setMtHT($row["MT_HT_TAXE"]);
        $garantie->setMtTTC($row["MT_TTC"]);
        $garantie->setMtAssu($row["MT_ASSU"]);
        return $garantie;
    }

}
