<?php
// src/AcsilServer/AppBundle/Entity/Document.php
namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
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
	 private $size;
	 
	 /**
     * @ORM\Column(type="integer")
     */
	 private $isProfilePicture;
	 
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;
    
	/**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
 
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner;
	
	    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadDate;
	
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
    public function setIsProfilePicture($isProfilePicture)
    {
        $this->isProfilePicture = $isProfilePicture;
    
        return $this;
    }
	
	 public function getId()
    {
        return $this->id;
    }

	public function getSize()
    {
        return $this->size;
    }
	
	public function getIsProfilePicture()
    {
        return $this->isProfilePicture;
    }
	
	 public function getName()
    {
        return $this->name;
    }

	public function getPath()
    {
        return $this->path;
    }

	public function getFile()
    {
        return $this->file;
    }
	
	public function getOwner()
    {
        return $this->owner;
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
     * Get uploadDate
     *
     * @return \DateTime 
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }
	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->setPath(sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension());
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
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
		if ($this->getIsProfilePicture() == 0)
		{
        return 'uploads/documents';
        }
		else
		{
		  return 'uploads/picture';
		}
	}
	
}