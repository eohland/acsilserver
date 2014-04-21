<?php

namespace AcsilServer\APIBundle\Entity;

class Copy
{
    /**
     * @var integer
     */
    private $fromId;

    /**
     * @var text
     */
    private $toPath;


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
     * Set toPath
     *
     * @param text $toPath
     * @return Copy
     */
    public function setToPath($toPath)
    {
        $this->toPath = $toPath;

        return $this;
    }

    /**
     * Get toPath
     *
     * @return text 
     */
    public function getToPath()
    {
        return $this->toPath;
    }
}
