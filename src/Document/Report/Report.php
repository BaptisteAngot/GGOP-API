<?php


namespace App\Document\Report;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Report
 * @package App\Document\Report
 * @MongoDB\Document(collection="Report")
 */
class Report
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $from_user;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $for_user;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

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
    public function getFromUser()
    {
        return $this->from_user;
    }

    /**
     * @param mixed $from_user
     */
    public function setFromUser($from_user): void
    {
        $this->from_user = $from_user;
    }

    /**
     * @return mixed
     */
    public function getForUser()
    {
        return $this->for_user;
    }

    /**
     * @param mixed $for_user
     */
    public function setForUser($for_user): void
    {
        $this->for_user = $for_user;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }
}