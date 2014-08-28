<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ChangePwd
{
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pwd;

    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
    
        return $this;
    }

    public function getPwd()
    {
        return $this->pwd;
    }
}
