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
     * @ORM\Column(type="string", length=255)
     */
    protected $realPath; 
	/**
     * @ORM\Column(type="string", length=255)
     */
    protected $chosenPath; 
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
     * @ORM\Column(type="datetime")
     */
    protected $lastModifDate;
	
	    /**
     * Set owner
     *
     * @param string $owner
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * @return Data
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
			    /**
     * Set realPath
     *
     * @param string $realPath
     * @return Data
     */
    public function setRealPath($realPath)
    {
        $this->realPath = $realPath;
    
        return $this;
    }

			    /**
     * Set chosenPath
     *
     * @param string $chosenPath
     * @return Data
     */
    public function setChosenPath($chosenPath)
    {
        $this->chosenPath = $chosenPath;
    
        return $this;
    }
	
	public function getRealPath()
    {
        return $this->realPath;
    }
	
	public function getChosenPath()
    {
	return $this->chosenPath;
    }
	
		/**
     * Set lastModifDate
     *
     * @param \DateTime $lastModifDate
     * @return Data
     */
    public function setLastModifDate(\DateTime $lastModifDate = NULL)
    {
        $this->lastModifDate = $lastModifDate;
    
        return $this;
    }

    /**
     * Get lastModifDate
     *
     * @return \DateTime 
     */
    public function getLastModifDate()
    {
        return $this->lastModifDate;
    }

}