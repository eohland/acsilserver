<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ChangePwd
{
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pwd;
	private $confirmPwd;

    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
    
        return $this;
    }

    public function getPwd()
    {
        return $this->pwd;
    }
	
	public function setConfirmPwd($pwd)
    {
        $this->confirmPwd = $confirmPwd;
    
        return $this;
    }

    public function getConfirmPwd()
    {
        return $this->confirmPwd;
    }
}
