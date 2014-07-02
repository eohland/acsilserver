<?php
// src/AcsilServer/AppBundle/Entity/Document.php
namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AcsilServer\AppBundle\Model\Data;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document extends Data
{


	 /**
     * @ORM\Column(type="integer")
     */
	 private $isProfilePicture;
	
 /**
     * @ORM\Column(type="integer")
     */
	 private $isShared;	
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
 

  	 /**
     * @ORM\Column(type="integer")
     */
    private $folder;

	     /* Set folder
     *
     * @param integer $folder
     * @return Document
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

	public function getFolder()
    {
        return $this->folder;
    }
	
			    /**
     * Set isProfilePicture
     *
     * @param integer $IsProfilePicture
     * @return Document
     */
	 public function setIsProfilePicture($isProfilePicture)
    {
        $this->isProfilePicture = $isProfilePicture;
    
        return $this;
    }

			    /**
     * Set isShared
     *
     * @param integer $IsShared
     * @return Document
     */
	 public function setIsShared($isShared)
    {
        $this->isShared = $isShared;
    
        return $this;
    }
	
	public function getIsProfilePicture()
    {
        return $this->isProfilePicture;
    }

	public function getIsShared()
    {
        return $this->isShared;
    }
	
	public function getFile()
    {
        return $this->file;
    }
	
		    /**
     * Set file
     *
     * @param \File $file
     * @return Document
     */
    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }

	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $tempPath = sha1(uniqid(mt_rand(), true));
			$this->setPath(substr($tempPath, -6).'.'.$this->file->guessExtension());
			$this->setSize($this->file->getClientSize());
 }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }
		if ($this->realPath)
			$this->file->move($this->getUploadRootDir().'/'.$this->realPath, $this->getPath());
		else
			$this->file->move($this->getUploadRootDir(), $this->getPath());
        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
    
	public function getAbsolutePath()
    {
	if ($this->realPath)
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->realPath.'/'.$this->path;
    else
	    return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
 
	}

    public function getWebPath()
    {
	if ($this->realPath)
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->realPath.'/'.$this->path;
	else
		return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    public function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
		if ($this->getIsProfilePicture() == 0)
		{
        return 'uploads/'.$this->getPseudoOwner();
        }
		else
		{
		  return 'uploads/picture';
		}
	}

}