<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
		$session = $request->getSession();
		
		// get the login error if there is one
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}
		
		return $this->render('AcsilServerAppBundle:Security:login.html.twig', 
			array(
				// last username entered by the user
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error'         => $error,
			));
    }

	public function registerAction(Request $request) 
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->get('session');
    	$factory = $this->get('security.encoder_factory');
    	$user = new Entity\User();
		$encoder = $factory->getEncoder($user);
		$form = $this->createForm(new Form\UserType(), $user);
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
			
			if ($form->isValid()) {
				$password = $encoder->encodePassword($form->getData()->getPassword(), $user->getSalt());
				// die(var_dump($form->getData()));
				
			echo '<pre>';
			die( var_dump( $form->getData(), $form->getData()->getFirstname(), $form->getData()->getRoles() ) );
			echo '</pre>';
			
								$user->setFirstname($form->getData()->getFirstname());
				$user->setLastname($form->getData()->getLastname());
				$user->setUsername($form->getData()->getFirstname().$form->getData()->getLastname().rand(1, 9999999));
				$user->setEmail($form->getData()->getEmail());
				$user->setPassword($password);
				$user->setRoles(json_encode(array($form->getData()->getRoles())));
				$user->setCreationDate(new \Datetime());
				
				$em->persist($user);
				$em->flush();
				
				$session->setFlash('notice', $this->get('translator')->trans('created.user'));
				return $this->redirect($this->generateUrl('_acsil'));
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
	
	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$adminToDelete = $em
			->getRepository('AcsilServerAppBundle:User')
			->findOneBy(array('id' => $id));
		$em->remove($adminToDelete);
		$em->flush();
		
		return $this->redirect($this->generateUrl('_acsiladmins'));
	}
}
