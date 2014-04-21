<?php

namespace AcsilServer\APIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Move
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Move
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="from_id", type="integer")
     */
    private $fromId;

    /**
     * @var string
     *
     * @ORM\Column(name="to_path", type="string", length=255)
     */
    private $toPath;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fromId
     *
     * @param integer $fromId
     * @return Move
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
     * @param string $toPath
     * @return Move
     */
    public function setToPath($toPath)
    {
        $this->toPath = $toPath;

        return $this;
    }

    /**
     * Get toPath
     *
     * @return string 
     */
    public function getToPath()
    {
        return $this->toPath;
    }
}
