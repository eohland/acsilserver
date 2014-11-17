<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\AppBundle\Entity;
use AcsilServer\AppBundle\Form;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

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
		$changeEmailForm = $this->createForm(new Form\ChangeEmailType(), new Entity\ChangeEmail());
		$changePictureForm = $this->createForm(new Form\ChangePictureType(), new Entity\ChangePicture());
		$changeQuestionForm = $this->createForm(new Form\ChangeQuestionType(), new Entity\ChangeQuestion());
		
		$listAlladmins = $em
			->getRepository('AcsilServerAppBundle:User')
			->findBy(array('roles' => array($superAdmin, $admin)));
		
		$listadmins = array();
		$aclProvider = $this -> get('security.acl.provider');
		foreach($listAlladmins as $admins) {
			if ($admins != $this -> getUser()) {
				$objectIdentity = ObjectIdentity::fromDomainObject($this->getUser());
				try {
					$acl = $aclProvider -> findAcl($objectIdentity);
				}
				catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
					continue;
				}

				$securityContext = $this -> get('security.context');
				$securityIdentity = UserSecurityIdentity::fromAccount($admins);
				$aces = $acl -> getObjectAces();
				foreach ($aces as $ace) {
					if ($ace -> getSecurityIdentity() == $securityIdentity) {
						if ($ace -> getMask() == MaskBuilder::MASK_OWNER) {
							array_push($listadmins, $admins);
						}
					}		
				}
			}
			else
				array_push($listadmins, $admins);
		}

		return $this->render('AcsilServerAppBundle:Acsil:admins.html.twig',
			array(
				'listadmins' =>$listadmins,
				'newUserForm' => $newUserForm->createView(),
				'changePwdForm' => $changePwdForm->createView(),
				'changeEmailForm' => $changeEmailForm->createView(),
				'changePictureForm' => $changePictureForm->createView(),
				'changeQuestionForm' => $changeQuestionForm->createView(),
				));
	}
}
