<?php


namespace App\Document\Request;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class Request
 * @package App\Document\RequestGGOP
 * @MongoDB\Document(collection="RequestGGOP")
 */
class RequestGGOP
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @@MongoDB\Field(type="string")
     */
    protected $type;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $from;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $to;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $requestValue;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from): void
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to): void
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getRequestValue()
    {
        return $this->requestValue;
    }

    /**
     * @param mixed $requestValue
     */
    public function setRequestValue($requestValue): void
    {
        $this->requestValue = $requestValue;
    }

}