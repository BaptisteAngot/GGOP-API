<?php


namespace App\Document\Team;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Team
 * @package App\Document\Team
 * @MongoDB\Document(collection="Team")
 */
class Team
{

    /**
     * @MongoDB\Id
     */
    public $id;

    /**
     * @MongoDB\Field(type="int")
     */
    public $creator_id;

    /**
     * @MongoDB\Field(type="string")
     */
    public $name;

    /**
     * @MongoDB\Field(type="int")
     */
    public $win_rate;

    /**
     * @MongoDB\Field(type="bool")
     */
    public $is_complete;

    /**
     * @MongoDB\Field(type="collection")
     */
    public $players;

    public function __construct()
    {
        $this->players = [];
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * @param mixed $creator_id
     */
    public function setCreatorId($creator_id): void
    {
        $this->creator_id = $creator_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getWinRate()
    {
        return $this->win_rate;
    }

    /**
     * @param mixed $win_rate
     */
    public function setWinRate($win_rate): void
    {
        $this->win_rate = $win_rate;
    }

    /**
     * @return mixed
     */
    public function getIsComplete()
    {
        return $this->is_complete;
    }

    /**
     * @param mixed $is_complete
     */
    public function setIsComplete($is_complete): void
    {
        $this->is_complete = $is_complete;
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

    public function addPlayers(TeamPlayer $playerTeam) {
        $this->players[] = $playerTeam;
    }
}

