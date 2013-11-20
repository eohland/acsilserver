<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;

/**
 * This controller contains all functions about security
 */
class SecurityController extends Controller
{
/**
 * This is the function for the authentification
 */
    public function loginAction(Request $request)
    {
		$session = $request->getSession();
		
    	$user = new Entity\User();
    	$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$form = $this->createForm(new Form\UserType(FALSE, TRUE), $user);
		$em = $this -> getDoctrine() -> getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		
		$isSuperAdmin = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findOneBy(array('roles' => $superAdmin));
		
		/**
		 * get the login error if there is one
         */		 
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}
		/**
		 * Verifiy the role of the user
		 */
		if ( ! $isSuperAdmin) {
			return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
				array(
					'newUserForm' => $form->createView(),
				));
		}
		
		return $this->render('AcsilServerAppBundle:Security:login.html.twig', 
			array(
				/**
				 * last username entered by the user
				 */
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error'         => $error,
			));
    }

/**
 * Create a new user
 */
	public function registerAction(Request $request, $registerAdmin)
	{
		$em = $this->getDoctrine()->getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		$admin = json_encode(array('ROLE_ADMIN'));
		$session = $this->get('session');
    	$factory = $this->get('security.encoder_factory');
    	$user = new Entity\User();
		if ($registerAdmin)
			$user->setUsertype('admin');
		$encoder = $factory->getEncoder($user);
		$form = $this->createForm(new Form\UserType(), $user);
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
			
			if ($form->isValid()) {
				$password = $encoder->encodePassword($form->getData()->getPassword(), $user->getSalt());
				$form->getData()->getUsertype() == 'user' ? $role = $admin : $role = $superAdmin;
				
				$user->setFirstname($form->getData()->getFirstname());
				$user->setLastname($form->getData()->getLastname());
				$user->setUsername($form->getData()->getFirstname().$form->getData()->getLastname().rand(1, 9999999));
				$user->setEmail($form->getData()->getEmail());
				$user->setPassword($password);
				$user->setRoles($role);
				$user->setUsertype($role);
				$user->setCreationDate(new \Datetime());
				
				$em->persist($user);
				$em->flush();
				
				$session->setFlash('notice', $this->get('translator')->trans('created.user'));
				if ($registerAdmin)
					return $this -> redirect($this->generateUrl('_home'));
				return $this->redirect($this->generateUrl('_acsiladmins'));
			}
		}
		
		return $this->render('AcsilServerAppBundle:Security:register.html.twig',
			array(
				'newUserForm' => $form->createView(),
			));
	}
	
    public function requestAction()
    {
        return $this->render('AcsilServerAppBundle:Security:request.html.twig', 
	        array(
	        	
			));
    }

/**
 * Change the role (Admin or User)
 */
	public function changeRoleAction($id, $role) {
		if ($this->getUser()->getId() == $id)
			return $this->redirect($this->generateUrl('_acsiladmins'));
		
		$em = $this->getDoctrine()->getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		$admin = json_encode(array('ROLE_ADMIN'));
		if ($role == 'admin')
			$setRole = $superAdmin;
		else if ($role == 'user')
			$setRole = $admin;
		$userToUpdate = $em
			->getRepository('AcsilServerAppBundle:User')
			->findOneBy(array('id' => $id));
		
		if ($userToUpdate) {
			$userToUpdate->setRoles($setRole);
			$em->flush();
		}
		
		return $this->redirect($this->generateUrl('_acsiladmins'));
	}

/**
 * Delete a user
 */
	public function deleteAction($id) {
		if ($this->getUser()->getId() == $id)
			return $this->redirect($this->generateUrl('_acsiladmins'));
		
		$em = $this->getDoctrine()->getManager();
		$adminToDelete = $em
			->getRepository('AcsilServerAppBundle:User')
			->findOneBy(array('id' => $id));
		
		$em->remove($adminToDelete);
		$em->flush();
		return $this->redirect($this->generateUrl('_acsiladmins'));
	}
}
