<?php


namespace App\Document\GameSession;


class GameSessionTeam
{
    public $winrate;

    public $players;

    public function __construct()
    {
        $this->players = [];
    }

    /**
     * @return mixed
     */
    public function getWinrate()
    {
        return $this->winrate;
    }

    /**
     * @param mixed $winrate
     */
    public function setWinrate($winrate): void
    {
        $this->winrate = $winrate;
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players): void
    {
        $this->players = $players;
    }

    public function addPlayers(Player $players)
    {
        $this->players[] = $players;
    }
}