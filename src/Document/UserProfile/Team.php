<?php


namespace App\Document\UserProfile;


class Team
{
    public $win;

    public $players;

    public function __construct()
    {
        $this->players = [];
    }

    /**
     * @return mixed
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * @param mixed $win
     */
    public function setWin($win): void
    {
        $this->win = $win;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    public function addPlayer(Player $player)
    {

    }

}