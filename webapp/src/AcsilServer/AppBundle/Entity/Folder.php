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
//	die(print_r($dir));
	$root = array();
	if (is_dir($dir))
{
	$root = scandir($dir);
//die(print_r($root));
	}
	foreach($root as $value) {
      if($value === '.' || $value === '..') {
        continue;
      }
	  
/*      if(is_file("$dir$value")) {
        $result[] = "$dir$value";
        continue;
      }
	if (@opendir("$dir$value"))
	die(print_r("YEAAAHHH"));
      if(is_dir("$dir$value")) {
	  die(print_r(var_dump(is_dir("$dir$value"))));
        $result[] = "$dir$value/";
      }*/
if ($value[0] == 'f')
{
 $result[] = "$dir$value";
} 
if ($value[0] == 'd')
{
 $result[] = "$dir/$value/";
} 
 foreach(self::listDirectory("$dir/$value/") as $value)
      {
        $result[] = $value;
      }
    }
	return $result;
	}
}