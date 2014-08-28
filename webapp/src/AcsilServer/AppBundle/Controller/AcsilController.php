<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;

class AcsilController extends Controller
{
/**
 * Return the main page of the account
 */
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

/**
 * Return the main page of User Management
 */ 
	public function adminsAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		$admin = json_encode(array('ROLE_ADMIN'));
		$user = new Entity\User();
    	$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$newUserForm = $this->createForm(new Form\UserType(), $user);
		$changePwdForm = $this->createForm(new Form\ChangePwdType(), new Entity\ChangePwd());
		
		$listadmins = $em
			->getRepository('AcsilServerAppBundle:User')
			->findBy(array('roles' => array($superAdmin, $admin)));
		
		return $this->render('AcsilServerAppBundle:Acsil:admins.html.twig',
			array(
				'listadmins' =>$listadmins,
				'newUserForm' => $newUserForm->createView(),
				'changePwdForm' => $changePwdForm->createView(),
			));
	}
}
