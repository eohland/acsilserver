<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * @ORM\Entity
 */
 class Module
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	 private $id;

	 	/**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
	 /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
	    /**
     * @ORM\Column(type="string")
     */
	private $code;

	/**
     * Set name
     *
     * @param string $name
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }
	    /**
     * Set code
     *
     * @param string $code
     * @return Module
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }
	
			    /**
     * Set file
     *
     * @param \File $file
     * @return Module
     */
    public function setFile($file)
    {
        $this->file = $file;
    
        return $this;
    }
	public function getName()
    {
        return $this->name;
    }
    public function getCode()
    {
        return $this->code;
    }
    public function getId()
    {
        return $this->id;
    }	
		public function getFile()
    {
        return $this->file;
    }
}
