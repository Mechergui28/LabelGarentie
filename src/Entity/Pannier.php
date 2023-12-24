<?php

namespace App\Entity;

class Pannier
{
    private $prelevement;
    private $accepte;

    /**
     * @return mixed
     */
    public function getPrelevement()
    {
        return $this->prelevement;
    }

    /**
     * @param mixed $prelevement
     */
    public function setPrelevement($prelevement): void
    {
        $this->prelevement = $prelevement;
    }

    /**
     * @return mixed
     */
    public function getAccepte()
    {
        return $this->accepte;
    }

    /**
     * @param mixed $accepte
     */
    public function setAccepte($accepte): void
    {
        $this->accepte = $accepte;
    }

}