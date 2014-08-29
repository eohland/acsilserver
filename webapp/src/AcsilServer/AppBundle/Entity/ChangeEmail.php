<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ChangeEmail
{
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
