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
	 /**
     * @ORM\Column(type="integer")
     */
	 private $isShared;	
/**
     * @ORM\Column(type="integer")
     */
	 private $fSize;

		    /**
     * Set fSize
     *
     * @param integer $fSize
     * @return Folder
     */
    public function setFSize($fSize)
    {
        $this->fSize = $fSize;
    
        return $this;
    }
	
		public function getFSize()
    {
        return $this->fSize;
    }
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
     * Set isShared
     *
     * @param integer $IsShared
     * @return Folder
     */
	 public function setIsShared($isShared)
    {
        $this->isShared = $isShared;
    
        return $this;
    }
	public function getIsShared()
    {
        return $this->isShared;
    }
	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
if ($this->getPath() == null)
{	
	$tempPath = sha1(uniqid(mt_rand(), true));
        $this->setPath('d'.substr($tempPath, -6));
 }
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
	    {
	if (file_exists($this->getUploadRootDir().'/'.$this->realPath.'/'.$this->path) == false)
		mkdir($this->getUploadRootDir().'/'.$this->realPath.'/'.$this->path);
		}
	else
		{
		if (file_exists($this->getUploadRootDir().'/'.$this->path) == false)
		mkdir($this->getUploadRootDir().'/'.$this->path);
		}
	}

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
	       if ($file = $this->getAbsolutePath()) {
            rmdir($file);
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
        return 'uploads/'.$this->getPseudoOwner();
    }	
	
	public function listDirectory($dir)
	{
	$result = array();
	$root = array();
	if (is_dir($dir))
{
	$root = scandir($dir);
	}
	foreach($root as $value) {
      if($value === '.' || $value === '..') {
        continue;
      }
	  
if ($value[0] == 'f')
{
 $result[] = "$dir/$value";
} 
if ($value[0] == 'd')
{
 $result[] = "$dir/$value";
} 
 foreach(self::listDirectory("$dir/$value") as $value)
      {
        $result[] = $value;
      }
    }
	return $result;
	}
}