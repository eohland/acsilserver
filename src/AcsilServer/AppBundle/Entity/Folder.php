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
     * @ORM\Column(type="string", length=255)
     */
    public $folder;

	     /* Set folder
     *
     * @param string $folder
     * @return Folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
        return $folder;
    }

	public function getFolder()
    {
        return $this->folder;
    }

	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->setPath(sha1(uniqid(mt_rand(), true)));
//			$this->setSize($this->file->getClientSize());
 }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
	    mkdir($this->getPath());
        //$this->file->move($this->getUploadRootDir(), $this->getPath());
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
    }
	
	public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/'.$this->getPseudoOwner();
    }
}