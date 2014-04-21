<?php
// src/AcsilServer/AppBundle/Entity/Folder.php
namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AcsilServer\AppBundle\Model\Data;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Folder extends Data
{
 	 /**
     * @ORM\Column(type="integer")
     */
    private $parentFolder;

	     /* Set parentFolder
     *
     * @param integer $parentFolder
     * @return Folder
     */
    public function setParentFolder($parentFolder)
    {
        $this->parentFolder = $parentFolder;
        return $this;
    }

	public function getParentFolder()
    {
        return $this->parentFolder;
    }

	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
		$tempPath = sha1(uniqid(mt_rand(), true));
        $this->setPath(substr($tempPath, -6));
 }
    

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
	$tempPath = $this->getUploadRootDir();
	if (file_exists($tempPath) == false)
		mkdir ($tempPath);
	if ($this->realPath)
	    mkdir($this->getUploadRootDir().'/'.$this->realPath.'/'.$this->path);
	else
		mkdir($this->getUploadRootDir().'/'.$this->path);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
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
        return 'uploads/'.$this->getPseudoOwner();
    }
}