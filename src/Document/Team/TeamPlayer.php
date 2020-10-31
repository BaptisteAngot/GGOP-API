<?php

namespace App\Document\Team;

class TeamPlayer
{
    public $user_id;

    public $user_pseudo;

    public $status;

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
     * @return mixed
     */
    public function getUserPseudo()
    {
        return $this->user_pseudo;
    }

    /**
     * @param mixed $user_pseudo
     */
    public function setUserPseudo($user_pseudo): void
    {
        $this->user_pseudo = $user_pseudo;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }
}