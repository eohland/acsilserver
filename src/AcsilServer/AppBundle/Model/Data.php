<?php
// src/AcsilServer/AppBundle/Model/Data.php

namespace AcsilServer\AppBundle\Model;
 
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
 
/**
 * @ORM\MappedSuperclass
 *
 */
abstract class Data
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

	/**
     * @ORM\Column(type="integer")
     */
	 protected $size;
	 
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;
     
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $pseudoOwner;
	
	    /**
     * @ORM\Column(type="datetime")
     */
    protected $uploadDate;
	
	    /**
     * Set owner
     *
     * @param string $owner
     * @return Document
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    
        return $this;
    }

		    /**
     * Set pseudoOwner
     *
     * @param string $pseudoOwner
     * @return Document
     */
    public function setPseudoOwner($pseudoOwner)
    {
        $this->pseudoOwner = $pseudoOwner;
    
        return $this;
    }

	
	    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

	    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

	    /**
     * Set id
     *
     * @param integer $id
     * @return Document
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

		    /**
     * Set size
     *
     * @param integer $size
     * @return Document
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }
	
			    /**
     * Set isProfilePicture
     *
     * @param integer $IsProfilePicture
     * @return Document
     */
	
	 public function getId()
    {
        return $this->id;
    }

	public function getSize()
    {
        return $this->size;
    }
		
	public function getName()
    {
        return $this->name;
    }

	public function getPath()
    {
        return $this->path;
    }
	
	public function getOwner()
    {
        return $this->owner;
    }

		public function getPseudoOwner()
    {
        return $this->pseudoOwner;
    }

	    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     * @return Document
     */
    public function setUploadDate(\DateTime $uploadDate = NULL)
    {
        $this->uploadDate = $uploadDate;
    
        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return \DateTime 
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }
}