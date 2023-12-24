<?php

namespace App\Entity;

class InfoGarantie
{
    private $day;
    private $month;
    private $year;
    private $ficheclient;
    private $ipid;

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day): void
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     */
    public function setMonth($month): void
    {
        $this->month = $month;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getFicheclient()
    {
        return $this->ficheclient;
    }

    /**
     * @param mixed $ficheclient
     */
    public function setFicheclient($ficheclient): void
    {
        $this->ficheclient = $ficheclient;
    }

    /**
     * @return mixed
     */
    public function getIpid()
    {
        return $this->ipid;
    }

    /**
     * @param mixed $ipid
     */
    public function setIpid($ipid): void
    {
        $this->ipid = $ipid;
    }

}