<?php


namespace App\Document\UserProfile;


class Player
{
    public $user_id;

    public $user_pseudo;

    public $champion;

    public $kill;

    public $deaths;

    public $assist;

    public $items;

    public $p_perk;

    public $s_perk;

    public $summoner_spells;

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
    public function getChampion()
    {
        return $this->champion;
    }

    /**
     * @param mixed $champion
     */
    public function setChampion($champion): void
    {
        $this->champion = $champion;
    }

    /**
     * @return mixed
     */
    public function getKill()
    {
        return $this->kill;
    }

    /**
     * @param mixed $kill
     */
    public function setKill($kill): void
    {
        $this->kill = $kill;
    }

    /**
     * @return mixed
     */
    public function getDeaths()
    {
        return $this->deaths;
    }

    /**
     * @param mixed $deaths
     */
    public function setDeaths($deaths): void
    {
        $this->deaths = $deaths;
    }

    /**
     * @return mixed
     */
    public function getAssist()
    {
        return $this->assist;
    }

    /**
     * @param mixed $assist
     */
    public function setAssist($assist): void
    {
        $this->assist = $assist;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items): void
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getPPerk()
    {
        return $this->p_perk;
    }

    /**
     * @param mixed $p_perk
     */
    public function setPPerk($p_perk): void
    {
        $this->p_perk = $p_perk;
    }

    /**
     * @return mixed
     */
    public function getSPerk()
    {
        return $this->s_perk;
    }

    /**
     * @param mixed $s_perk
     */
    public function setSPerk($s_perk): void
    {
        $this->s_perk = $s_perk;
    }

    /**
     * @return mixed
     */
    public function getSummonerSpells()
    {
        return $this->summoner_spells;
    }

    /**
     * @param mixed $summoner_spells
     */
    public function setSummonerSpells($summoner_spells): void
    {
        $this->summoner_spells = $summoner_spells;
    }


}