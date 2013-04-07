<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;

class AcsilController extends Controller
{
    public function indexAction() {
		//die(var_dump($this->getUser()));
    	$user = $this->get('security.context')->getToken()->getUser();
		$userRole = $user->getRoles();
    	//die(var_dump($user = $this->get('security.context')->getToken()->getUser()));
    	
		return $this->render('AcsilServerAppBundle:Acsil:home.html.twig', 
			array(
				'user' => $user,
				'userRole' => $userRole[0],
			));
    }
	
	public function adminsAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		$admin = json_encode(array('ROLE_ADMIN'));
		$editForm = $this->createForm(new Form\UserType($this->getUser()), new Entity\User());
		
		if ($request->isMethod('POST')) {
			
		}
		
		$listadmins = $em
			->getRepository('AcsilServerAppBundle:User')
			->findBy(array('roles' => array($superAdmin, $admin)));
		
		return $this->render('AcsilServerAppBundle:Acsil:admins.html.twig',
			array(
				'listadmins' =>$listadmins,
				'editForm' => $editForm->createView(),
			));
	}
}
