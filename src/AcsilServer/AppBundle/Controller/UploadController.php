<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use AcsilServer\AppBundle\Entity\ShareFile;
use AcsilServer\AppBundle\Form\ShareFileType;
use AcsilServer\AppBundle\Form\DocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * This controller contains all the functions in touch with upload
 */

class UploadController extends Controller {

/**
 * function in touch with the main page of the FileManagement part 
 */ 

	public function manageAction() {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$shareForm = $this -> createForm(new ShareFileType(), new ShareFile());
		$form = $this -> createForm(new DocumentType(), new Document());
		$securityContext = $this -> get('security.context');

		$listAllfiles = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findBy(array('isProfilePicture' => 0));
		
		$listusers = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findAll();
     /**
      * Get informations about files
      */	
		$listfiles = array();
		$shareinfos = array();
		foreach ($listAllfiles as $file) {
			if ($securityContext -> isGranted('EDIT', $file) === TRUE 
				|| $securityContext -> isGranted('VIEW', $file) === TRUE) {
				
				$listUserFileInfos = array();
				$sharedFileUserInfos = array();
				if ($securityContext -> isGranted('OWNER', $file) === TRUE) {
					foreach ($listusers as $user) {
						$aclProvider = $this -> container -> get('security.acl.provider');
						$objectIdentity = ObjectIdentity::fromDomainObject($file);
						$acl = $aclProvider -> findAcl($objectIdentity);
						$securityContext = $this -> container -> get('security.context');
						$securityIdentity = UserSecurityIdentity::fromAccount($user);
						$aces = $acl -> getObjectAces();
						
						if ($user != $this -> getUser()) {
							$rights = NULL;
							foreach ($aces as $ace) {
								if ($ace -> getMask() == MaskBuilder::MASK_VIEW) {
									$rights = "VIEW";
								}
								if ($ace -> getMask() == MaskBuilder::MASK_EDIT) {
									$rights = "EDIT";
								}
							}
							if ($rights != NULL)
								array_push($sharedFileUserInfos, array("user" => $user, "rights" => $rights));
						}
					}
				}
				if (count($sharedFileUserInfos) > 0)
					$listUserFileInfos = array("info" => $file, "sharedFileUserInfos" => $sharedFileUserInfos);
				else 
					$listUserFileInfos = array("info" => $file, "sharedFileUserInfos" => '');
				array_push($listfiles, $listUserFileInfos);
			}
		}
		
        //		echo '<pre>';
        //		die(print_r($listfiles));
        //		echo '</pre>';
		
		return $this -> render('AcsilServerAppBundle:Acsil:files.html.twig', 
			array(
				'listfiles' => $listfiles, 
				'listusers' => $listusers, 
				'form' => $form -> createView(), 
				'shareForm' => $shareForm -> createView(), 
			));
	}

/**
 * Function to upload a new file
 */	
	
	/**
	 * @Template()
	 */
	public function uploadAction() {
		$em = $this -> getDoctrine() -> getManager();
    /**
     * Create and fill a new document object
    */
		$document = new Document();
		$request = $this -> getRequest();
		$uploadedFile = $request -> files -> get('acsilserver_appbundle_documenttype');
		$parameters = $request -> request -> get('acsilserver_appbundle_documenttype');
		$document -> setFile($uploadedFile['file']);
		$filename = $parameters['name'];
		$document -> setName($filename);
		$document -> setIsProfilePicture(0);
		if ($document -> getFile() == null) {
			return $this -> redirect($this -> generateUrl('_upload'));
		}
		if ($document -> getName() == null) {
			$document -> setName($document -> getFile() -> getClientOriginalName());
		}
		$document -> setOwner($this -> getUser() -> getEmail());
		$document -> setuploadDate(new \DateTime());
		$document -> setPseudoOwner($this -> getUser() -> getUsername());

		$em -> persist($document);
		$em -> flush();
    /**
    * Set the rights
    */
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);

		return $this -> redirect($this -> generateUrl('_managefile'));
	}

/**
 * Share a file
 */

	public function shareAction(Request $request, $id) {
		$parameters = $request -> request -> get('acsilserver_appbundle_sharefiletype');
		$friendName = $parameters['userMail'];
		$right = $parameters['rights'];
		
		if ($friendName == NULL) {
			return $this -> redirect($this -> generateUrl('_managefile'));
		}
		$em = $this -> getDoctrine() -> getManager();
		$friend = $em -> getRepository('AcsilServerAppBundle:User') -> findOneByEmail($friendName);

		if (!$friend) {
			throw $this -> createNotFoundException('No user found for name ' . $friendName);
		}

		$document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id);

		if (!$document) {
			throw $this -> createNotFoundException('No document found for id ' . $id);
		}

		$builder = new MaskBuilder();
		if ($right == "EDIT") {
			$builder -> add('view') -> add('edit') -> add('delete');
		}
		if ($right == "VIEW") {
			$builder -> add('view');
		}
        /**
		 * Set the rights for the other user 
		*/
		$aclProvider = $this -> container -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> findAcl($objectIdentity);
		$securityContext = $this -> container -> get('security.context');
		$securityIdentity = UserSecurityIdentity::fromAccount($friend);
		$aces = $acl -> getObjectAces();

		foreach ($aces as $index => $ace) {
			if ($ace -> getSecurityIdentity() == $securityIdentity) {
				$acl -> deleteObjectAce($index);
				break;
			}
		}
		if ($right != "DELETE") {
			$mask = $builder -> get();
			var_dump($builder -> get());
			$acl -> insertObjectAce($securityIdentity, $mask);
		}
		$aclProvider -> updateAcl($acl);
		return $this -> redirect($this -> generateUrl('_managefile'));
	}

/**
 * Upload a picture for the account
*/
	/**
	 * @Template()
	 */
	public function pictureAction() {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$form = $this -> createFormBuilder($document) -> add('file') -> getForm();
        /**
		 * Create and fill a new document object
		*/ 
		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());
			if ($form -> isValid()) {
				if ($document -> getFile() == null) {
					return $this -> redirect($this -> generateUrl('_upload_picture'));
				}
				$picturePath = $this -> getUser() -> getPictureAccount();
				if (!empty($picturePath)) {
					$query = $em -> createQuery('SELECT d FROM AcsilServerAppBundle:Document d WHERE d.name = :docName AND d.isProfilePicture = 1') -> setParameter('docName', 'avatar-' . $this -> getUser() -> getEmail());
					$fileToDelete = $query -> getSingleResult();
					$em -> remove($fileToDelete);
				}
				$document -> setIsProfilePicture(1);
				$document -> setName('avatar-' . $this -> getUser() -> getEmail());
				$document -> setOwner($this -> getUser() -> getEmail());
				$document -> setuploadDate(new \Datetime());
				$em -> persist($document);
				$em -> flush();

				$usr = $this -> getUser();
				$usr -> setPictureAccount($document -> getWebPath());
				$em -> flush();

				$aclProvider = $this -> get('security.acl.provider');
				$objectIdentity = ObjectIdentity::fromDomainObject($document);
				$acl = $aclProvider -> createAcl($objectIdentity);
				$securityContext = $this -> get('security.context');
				$user = $securityContext -> getToken() -> getUser();
				$user -> setPictureAccount($document -> getWebPath());
        /**
         * Resize picture
        */		 
				$link = $document -> getAbsolutePath();
				$ImageNews = $_FILES['form']['name'];
				$ImageNews = getimagesize($link);
				$ImageChoisie = imagecreatefromjpeg($link);
				$TailleImageChoisie = getimagesize($link);
				$NouvelleLargeur = 60;
				$NouvelleHauteur = (($TailleImageChoisie[1] * (($NouvelleLargeur) / $TailleImageChoisie[0])));
				$NouvelleImage = imagecreatetruecolor($NouvelleLargeur, $NouvelleHauteur) or die("Erreur");
				imagecopyresampled($NouvelleImage, $ImageChoisie, 0, 0, 0, 0, $NouvelleLargeur, $NouvelleHauteur, $TailleImageChoisie[0], $TailleImageChoisie[1]);
				imagedestroy($ImageChoisie);
				imagejpeg($NouvelleImage, $document -> getAbsolutePath(), 100);

				$securityIdentity = UserSecurityIdentity::fromAccount($user);
				$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
				$aclProvider -> updateAcl($acl);
				return $this -> redirect($this -> generateUrl('_acsil'));

			}
		}

		return array('form' => $form -> createView());
	}

/**
 * Change the rights which have a user on a file
*/ 
	
	public function updateRightsAction($fileId, $userId, $newRights) {
		
		$em = $this -> getDoctrine() -> getManager();
		$friend = $em -> getRepository('AcsilServerAppBundle:User') -> findOneById($userId);

		if (!$friend) {
			throw $this -> createNotFoundException('No user found for id ' . $userId);
		}

		$document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($fileId);

		if (!$document) {
			throw $this -> createNotFoundException('No document found for id ' . $fileId);
		}

		$builder = new MaskBuilder();
		if ($newRights == "EDIT") {
			$builder -> add('view') -> add('edit') -> add('delete');
		}
		if ($newRights == "VIEW") {
			$builder -> add('view');
		}

		$aclProvider = $this -> container -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> findAcl($objectIdentity);
		$securityContext = $this -> container -> get('security.context');
		$securityIdentity = UserSecurityIdentity::fromAccount($friend);
		$aces = $acl -> getObjectAces();
    /**
	 * Delete old rights
	*/
		foreach ($aces as $index => $ace) {
			if ($ace -> getSecurityIdentity() == $securityIdentity) {
				$acl -> deleteObjectAce($index);
				break;
			}
		}
	/**
	 * Insert the new rights
	*/
		if ($newRights != "DELETE") {
			$mask = $builder -> get();
			var_dump($builder -> get());
			$acl -> insertObjectAce($securityIdentity, $mask);
		}
		$aclProvider -> updateAcl($acl);
		return $this -> redirect($this -> generateUrl('_managefile'));
		
		
		
		return $this -> redirect($this -> generareUrl('_managefile'));
	}

/**
 * Delete a file
*/
	public function deleteAction($id) {
		$securityContext = $this -> get('security.context');
		$em = $this -> getDoctrine() -> getManager();
		$fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
		if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
			throw new AccessDeniedException();
		}

		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
		$aclProvider -> deleteAcl($objectIdentity);
		$em -> remove($fileToDelete);
		$em -> flush();

		return $this -> redirect($this -> generateUrl('_managefile'));
	}

}
