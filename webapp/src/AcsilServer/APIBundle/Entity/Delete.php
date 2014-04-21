<?php

namespace AcsilServer\APIBundle\Entity;

class Delete
{
    /**
     * @var integer
     */
    private $deleteId;

    /**
     * Set deleteId
     *
     * @param integer $deleteId
     * @return Copy
     */
    public function setDeleteId($deleteId)
    {
        $this->deleteId = $deleteId;

        return $this;
    }

    /**
     * Get delteId
     *
     * @return integer 
     */
    public function getDeleteId()
    {
        return $this->deleteId;
    }
}
