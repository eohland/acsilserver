<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ChangeQuestion
{
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $question;
	
	 /**
     * @ORM\Column(type="string", length=50)
     */
	private $answer;

    public function setQuestion($question)
    {
        $this->question = $question;
    
        return $this;
    }

    public function getQuestion()
    {
        return $this->question;
    }
	
	public function setAnswer($answer)
    {
        $this->answer = $answer;
    
        return $this;
    }

    public function getAnswer()
    {
        return $this->answer;
    }
}
