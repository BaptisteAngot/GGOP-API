<?php


namespace App\Document\UserProfile;


class Reputation
{
    public $ratio;

    public $honors;

    public $reports;

    /**
     * @return mixed
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param mixed $ratio
     */
    public function setRatio($ratio): void
    {
        $this->ratio = $ratio;
    }

    /**
     * @return mixed
     */
    public function getHonors()
    {
        return $this->honors;
    }

    /**
     * @param mixed $honors
     */
    public function setHonors($honors): void
    {
        $this->honors = $honors;
    }

    public function addHonors(Honor $honor)
    {
        $this->honors[] = $honor;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param mixed $reports
     */
    public function setReports($reports): void
    {
        $this->reports = $reports;
    }

    public function addReports(Report $report)
    {
        $this->reports[] = $report;
    }
}