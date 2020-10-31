<?php


namespace App\Document\UserProfile;


class Game
{
    public $date;

    public $result;

    public $teams;

    public function __construct() {
        $this->teams = [];
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getTeams(): array
    {
        return $this->teams;
    }

    /**
     * @param array $teams
     */
    public function setTeams(array $teams): void
    {
        $this->teams = $teams;
    }

    public function addTeams(Team $teams)
    {
        $this->teams[] = $teams;
    }
}