<?php


namespace App\Document\UserProfile;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class UserProfile
 * @package App\Document\UserProfile
 * @MongoDB\Document(collection="UserProfile")
 */
class UserProfile
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $user_id;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $reputation;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $game_history;

    public function __construct() {
        $this->reputation = [];
        $this->game_history = [];
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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return array
     */
    public function getReputation(): array
    {
        return $this->reputation;
    }

    /**
     * @param array $reputation
     */
    public function setReputation(array $reputation): void
    {
        $this->reputation = $reputation;
    }

    public function addReputation(Reputation $reputation)
    {
        $this->reputation[] = $reputation;
    }

    /**
     * @return array
     */
    public function getGameHistory(): array
    {
        return $this->game_history;
    }

    /**
     * @param array $game_history
     */
    public function setGameHistory(array $game_history): void
    {
        $this->game_history = $game_history;
    }

    public function addGameHistory(Game $game)
    {
        $this->game_history[] = $game;
    }
}