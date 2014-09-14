<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;
use AcsilServer\AppBundle\Entity\Document;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

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
			$errorForm = '';
			return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
				array(
					'newUserForm' => $form->createView(),
					'errorForm' => $errorForm,
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
		
		$isSuperAdmin = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findOneBy(array('roles' => $superAdmin));
			
		if ( ! $isSuperAdmin)
			$form = $this->createForm(new Form\UserType(FALSE, TRUE), $user);
		else
			$form = $this->createForm(new Form\UserType(), $user);

		$errorForm = '';
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
			
			if ($form->isValid()) {
				//Check if email is already use
				$query = $em -> createQuery('SELECT u FROM AcsilServerAppBundle:User u WHERE u.email = :userEmail') -> setParameter('userEmail', $form ->getData()->getEmail());
				if ($query -> getOneOrNullResult() != NULL) {
					$errorForm = 'errorMail';
					if ( ! $isSuperAdmin)
						return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
							array(
									'newUserForm' => $form->createView(),
									'errorForm' => $errorForm,
								));
					return $this->render('AcsilServerAppBundle:Security:register.html.twig',
						array(
								'newUserForm' => $form->createView(),
								'errorForm' => $errorForm,
							));
					}
				//Check if pwd and confirm pwd are equal
				if (strlen($form->getData()->getPassword()) < 6) {
					$errorForm = 'errorSizePwd';
					if ( ! $isSuperAdmin)
						return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
							array(
									'newUserForm' => $form->createView(),
									'errorForm' => $errorForm,
								));
					return $this->render('AcsilServerAppBundle:Security:register.html.twig',
						array(
								'newUserForm' => $form->createView(),
								'errorForm' => $errorForm,
							));
				}
				//Check if pwd and confirm pwd are equal
				if ($form->getData()->getPassword() != $form->getData()->getConfirmPassword()) {
					$errorForm = 'errorPwd';
					if ( ! $isSuperAdmin)
						return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
							array(
									'newUserForm' => $form->createView(),
									'errorForm' => $errorForm,
								));
					return $this->render('AcsilServerAppBundle:Security:register.html.twig',
						array(
								'newUserForm' => $form->createView(),
								'errorForm' => $errorForm,
							));
				}
				//Check if pictureAccount is .jpeg .jpg .png or .gif	
				if ($form ->getData()->getPictureAccount()->getMimeType() != "image/jpeg" && $form ->getData()->getPictureAccount()->getMimeType() != "image/jpg" &&
					$form ->getData()->getPictureAccount()->getMimeType() != "image/png" && $form ->getData()->getPictureAccount()->getMimeType() != "image/gif") {
					$errorForm = 'errorPicture';
					if ( ! $isSuperAdmin)
						return $this->render('AcsilServerAppBundle:Security:registerAdmin.html.twig',
							array(
									'newUserForm' => $form->createView(),
									'errorForm' => $errorForm,
								));
					return $this->render('AcsilServerAppBundle:Security:register.html.twig',
						array(
								'newUserForm' => $form->createView(),
								'errorForm' => $errorForm,
							));	
				}
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
				
			//	$em->flush();
				
                // deleted from symphony 2.4
				//$session->setFlash('notice', $this->get('translator')->trans('created.user'));
				$session->getFlashBag()->add('notice', $this->get('translator')->trans('created.user'));
				
				
				
    /**
     * Create and fill a new document object
    */
		$document = new Document();
		$request = $this -> getRequest();
		$uploadedFile = $request -> files -> get('acsilserver_appbundle_usertype');
		//$parameters = $request -> request -> get('acsilserver_appbundle_documenttype');
		//die(print_r($uploadedFile));
		$document -> setFile($uploadedFile['pictureAccount']);
		$document -> setIsShared(0);
		$document -> setName('avatar-' . $form->getData()->getEmail());
		$document -> setOwner($form->getData()->getEmail());
		$document -> setuploadDate(new \DateTime());
		$document -> setPseudoOwner($form->getData() -> getUsername());
		$document -> setIsProfilePicture(1);
		$document -> setFolder(0);
		$document -> setRealPath("");
		$document -> setChosenPath("");		
		$em -> persist($document);
		$user->setPictureAccount($document->getWebPath());
		$em->persist($user);
		$em -> flush();
    /**
    * Set the rights
    */
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		//$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);

				
				
				if ($registerAdmin)
					return $this -> redirect($this->generateUrl('_home'));
				return $this->redirect($this->generateUrl('_acsiladmins'));
			}
		}
		
		return $this->render('AcsilServerAppBundle:Security:register.html.twig',
			array(
					'newUserForm' => $form->createView(),
					'errorForm' => $errorForm,
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
		
		/*	Delete picture account */
		$query = $em -> createQuery('SELECT d FROM AcsilServerAppBundle:Document d WHERE d.name = :docName AND d.isProfilePicture = 1') -> setParameter('docName', 'avatar-' . $adminToDelete -> getEmail());
		$fileToDelete = $query -> getSingleResult();
		$em -> remove($fileToDelete);
		$em->flush();
		
		$em->remove($adminToDelete);
		$em->flush();
		return $this->redirect($this->generateUrl('_acsiladmins'));
	}
	
/**
* Change pwd
*/
	public function changePwdAction(Request $request) {
	$em = $this->getDoctrine()->getManager();
	$user = $this->getUser();
	
	$parameters = $request -> request -> get('acsilserver_appbundle_changepwdtype');
	$pwd = $parameters['pwd'];
	
	$factory = $this->get('security.encoder_factory');
	$encoder = $factory->getEncoder($user);
  
    $password = $encoder->encodePassword($pwd, $user->getSalt());
    $user->setPassword($password);
    $em->persist($user);
    $em->flush();

	return $this -> redirect($this -> generateUrl('_acsiladmins'));	
	}
	
/**
* Change e-mail
*/
	public function changeEmailAction(Request $request) {
	$em = $this->getDoctrine()->getManager();
	$user = $this->getUser();
	
	$parameters = $request -> request -> get('acsilserver_appbundle_changeemailtype');
	$email = $parameters['email'];
	
    $user->setEmail($email);
    $em->persist($user);
    $em->flush();

	return $this -> redirect($this -> generateUrl('_acsiladmins'));	
	}
	
/**
 * Change picture account
*/
	public function changePictureAction(Request $request) {
	
	$em = $this->getDoctrine()->getManager();
	$user = $this->getUser();
	
	/*
	*	Delete old picture
	*/
	$query = $em -> createQuery('SELECT d FROM AcsilServerAppBundle:Document d WHERE d.name = :docName AND d.isProfilePicture = 1') -> setParameter('docName', 'avatar-' . $user -> getEmail());
	$fileToDelete = $query -> getSingleResult();
	$em -> remove($fileToDelete);
	$em->flush();
	
   /**
     * Create and fill a new document for new picture
    */
	$document = new Document();

	$uploadedFile = $request -> files -> get('acsilserver_appbundle_changepicturetype');
	$document -> setFile($uploadedFile['picture']);
	$document -> setIsShared(0);
	$document -> setName('avatar-' . $user->getEmail());
	$document -> setOwner($user->getEmail());
	$document -> setuploadDate(new \DateTime());
	$document -> setPseudoOwner($user -> getUsername());
	$document -> setIsProfilePicture(1);
	$document -> setFolder(0);
	$document -> setRealPath("");
	$document -> setChosenPath("");	
	
	$em -> persist($document);
	$user->setPictureAccount($document->getWebPath());
		
	$em->persist($user);
	$em -> flush();
		
	$aclProvider = $this -> get('security.acl.provider');
	$objectIdentity = ObjectIdentity::fromDomainObject($document);
	$acl = $aclProvider -> createAcl($objectIdentity);
	$securityContext = $this -> get('security.context');
	$user = $securityContext -> getToken() -> getUser();
	$user -> setPictureAccount($document -> getWebPath());
	
	return $this -> redirect($this -> generateUrl('_acsiladmins'));	
	}
}	