<?php


namespace App\Document\GameSession;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class GameSession
 * @package App\Document\GameSession
 * @MongoDB\Document(collection="GameSession")
 */
class GameSession
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $creator_id;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $date_debut;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $date_fin;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $team;

    public function __construct() {
        $this->team = [];
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
    public function getDateDebut()
    {
        return $this->date_debut;
    }

    /**
     * @param mixed $date_debut
     */
    public function setDateDebut($date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->date_fin;
    }

    /**
     * @param mixed $date_fin
     */
    public function setDateFin($date_fin): void
    {
        $this->date_fin = $date_fin;
    }



    /**
     * @return array
     */
    public function getTeam(): array
    {
        return $this->team;
    }

    /**
     * @param array $team
     */
    public function setTeam(array $team): void
    {
        $this->team = $team;
    }

    public function addTeam(GameSessionTeam $team) {
        $this->team[] = $team;
    }
}