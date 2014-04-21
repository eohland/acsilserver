<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class ShareFile
{
    private $userMail;
    private $rights;


    public function setUserMail($userMail)
    {
        $this->userMail = $userMail;
    
        return $this;
    }

    public function getUserMail()
    {
        return $this->userMail;
    }

    public function setRights($rights)
    {
        $this->rights = $rights;
    
        return $this;
    }

    public function getRights()
    {
        return $this->rights;
    }
}
