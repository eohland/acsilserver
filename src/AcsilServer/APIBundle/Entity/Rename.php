<?php

namespace AcsilServer\APIBundle\Entity;

class Rename
{
    /**
     * @var integer
     */
    private $fromId;

    /**
     * @var text
     */
    private $toName;


    /**
     * Set fromId
     *
     * @param integer $fromId
     * @return Copy
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Get fromId
     *
     * @return integer 
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set toName
     *
     * @param text $toName
     * @return Copy
     */
    public function setToName($toName)
    {
        $this->toName = $toName;

        return $this;
    }

    /**
     * Get toName
     *
     * @return text 
     */
    public function getToName()
    {
        return $this->toName;
    }
}
