<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ForgotPwd
{
    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;
	
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $question;
	
	 /**
     * @ORM\Column(type="string", length=50)
     */
	private $answer;

   /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
	
	  /**
     * Set question
     *
     * @param string $question
     * @return User
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    
        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */	
    public function getQuestion()
    {
        return $this->question;
    }
	
	/**
     * Set answer
     *
     * @param string $answer
     * @return User
     */
	public function setAnswer($answer)
    {
        $this->answer = $answer;
    
        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */		
    public function getAnswer()
    {
        return $this->answer;
    }
}
