<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AcsilController extends Controller
{
    public function indexAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();
		$userRole = $user->getRoles();
    	//die(var_dump($user = $this->get('security.context')->getToken()->getUser()));
		return $this->render('AcsilServerAppBundle:Acsil:home.html.twig', 
			array(
				'user' => $user,
				'userRole' => $userRole[0],
			));
    }
}
