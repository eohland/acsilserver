<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 */
 class MoveFile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	 private $id;
		 /**
     * @ORM\Column(type="integer")
     */
	 private $fileId;

	 /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
	    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
	private $path;
	
		 /**
     * @ORM\Column(type="integer")
     */
	private $isFolder;
		 /**
     * @ORM\Column(type="integer")
     */
	private $action;
	    /**
     * Set name
     *
     * @param string $name
     * @return MoveFile
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }
	    /**
     * Set path
     *
     * @param string $path
     * @return MoveFile
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }
		    /**
     * Set action
     *
     * @param integer $action
     * @return MoveFile
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }
		    /**
     * Set action
     *
     * @param integer $isFolder
     * @return MoveFile
     */
    public function setIsFolder($isFolder)
    {
        $this->isFolder = $isFolder;
    
        return $this;
    }
		    /**
     * Set fileId
     *
     * @param integer $fileId
     * @return MoveFile
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    
        return $this;
    }
	public function getName()
    {
        return $this->name;
    }
    public function getPath()
    {
        return $this->path;
    }
	 public function getAction()
    {
        return $this->action;
    }
	 public function getIsFolder()
    {
        return $this->isFolder;
    }
    public function getFileId()
    {
        return $this->fileId;
    }	
}
