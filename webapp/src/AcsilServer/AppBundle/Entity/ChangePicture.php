<?php

namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
 
class ChangePicture
{

    private $picture;

    public function setPicture($picture)
    {
        $this->picture = $picture;
    
        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }
}
