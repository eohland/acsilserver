<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use AcsilServer\AppBundle\Entity\Folder;
use AcsilServer\AppBundle\Entity\ShareFile;
use AcsilServer\AppBundle\Form\ShareFileType;
use AcsilServer\AppBundle\Form\DocumentType;
use AcsilServer\AppBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use \ZipArchive;
use \RecursiveIteratorIterator;
/**
 * This controller contains all the functions in touch with upload
 */

class UploadController extends Controller {

/**
 * function in touch with the main page of the FileManagement part 
 */ 

	public function manageAction($folderId) {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$shareForm = $this -> createForm(new ShareFileType(), new ShareFile());
		$form = $this -> createForm(new DocumentType(), new Document());
		$folderForm = $this -> createForm(new FolderType(), new Folder());
		$securityContext = $this -> get('security.context');

		$listAllfiles = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findBy(array('folder' => $folderId, 'isProfilePicture' => 0));

		$listusers = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findAll();
		
		$listfolders = $em
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findBy(array('parentFolder' => $folderId, 'owner' => $this->getUser()->getEmail()));
		if ($folderId == 0)
		{
		$parentId = 0;
		}
		else
		{
		$currentFolder = $em
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $folderId, 'owner' => $this->getUser()->getEmail()));
		$parentId = $currentFolder->getParentFolder();
		}
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
									if ($ace -> getMask() == 13) {
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
		
		return $this -> render('AcsilServerAppBundle:Acsil:files.html.twig', 
			array(
				'listfiles' => $listfiles, 
				'listusers' => $listusers,
				'folderId' => $folderId,
				'listfolders' => $listfolders,
				'parentId' => $parentId,
				'form' => $form -> createView(), 
				'shareForm' => $shareForm -> createView(),
				'folderform' => $folderForm -> createView(),
			));
	}

/**
 * Function to upload a new file
 */	
	
	/**
	 * @Template()
	 */
	public function uploadAction($folderId) {
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
			return $this -> redirect($this -> generateUrl('_upload', array(
            'folderId' => $folderId,
        )));
		}
		if ($document -> getName() == null) {
			$document -> setName($document -> getFile() -> getClientOriginalName());
		}
		$document -> setOwner($this -> getUser() -> getEmail());
		$document -> setuploadDate(new \DateTime());
		$document -> setPseudoOwner($this -> getUser() -> getUsername());
		$document -> setFolder($folderId);
		
		
		$tempId = $folderId;
		$totalPath = "";
		while ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent found for id : '.$id
        );
		}
		$totalPath = $parent->getPath().'/'.$totalPath;
		$tempId = $parent->getParentFolder();
		}
		
		$document -> setRealPath($totalPath);
		
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

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}

/**
 * Share a file
 */

	public function shareAction(Request $request, $id) {
		$parameters = $_GET['acsilserver_appbundle_sharefiletype'];
		$friendName = $parameters['userMail'];
		$right = $parameters['rights'];
		if ($friendName == NULL || $right == NULL) {
			throw $this -> createNotFoundException('Invalid data.');
			return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => 0,
        )));
		}
		$em = $this -> getDoctrine() -> getManager();
		$friend = $em -> getRepository('AcsilServerAppBundle:User') -> findOneByEmail($friendName);

		if (!$friend) {
			throw $this -> createNotFoundException('No user found for name ' . $friendName);
		}

		$document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id);
        $folderId = $document->getFolder();
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
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
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
		$folderId = $document->getFolder();
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
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}

/**
 * Delete a file
*/
	public function deleteAction($id) {
		$securityContext = $this -> get('security.context');
		$em = $this -> getDoctrine() -> getManager();
		$fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
		if (!$fileToDelete) {
			throw $this -> createNotFoundException('No document found for id ' . $id);
		}
		$folderId = $fileToDelete->getFolder();
		if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
			throw new AccessDeniedException();
		}

		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
		$aclProvider -> deleteAcl($objectIdentity);
		$em -> remove($fileToDelete);
		$em -> flush();

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}

	/**
 * Function to create a new folder
 */	
	
	/**
	 * @Template()
	 */
	public function folderAction($folderId) {
		$em = $this -> getDoctrine() -> getManager();
    /**
     * Create and fill a new folder object
    */
		$folder = new Folder();
		$request = $this -> getRequest();
		$parameters = $request -> request -> get('acsilserver_appbundle_foldertype');
		$foldername = $parameters['name'];
		$folder -> setName($foldername);
		$folder -> setOwner($this -> getUser() -> getEmail());
		$folder -> setuploadDate(new \DateTime());
		$folder -> setPseudoOwner($this -> getUser() -> getUsername());
		$folder -> setParentFolder($folderId);

		
		$tempId = $folderId;
		$totalPath = "";
		while ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent found for id : '.$tempId
        );
		}
		$totalPath = $parent->getPath().'/'.$totalPath;
		$tempId = $parent->getParentFolder();
		}
		
		$folder-> setRealPath($totalPath);
		
		
		$em -> persist($folder);
		$em -> flush();
    /**
    * Set the rights
    */
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($folder);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	/**
	 * @Template()
	 */
	public function downloadAction($id) {
	$em = $this -> getDoctrine() -> getManager();
	$document = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('id' => $id, 'isProfilePicture' => 0));
	$response = new Response();
	$response->headers->set('Content-type', 'application/octet-stream');
	if ($document->getRealPath())
	    $path = $document->getUploadRootDir().'/'.$document->getRealPath().'/'.$document->getPath();
	else
	    $path = $document->getUploadRootDir().'/'.$document->getPath();
	$response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $document->getName().'.'.pathinfo($path, PATHINFO_EXTENSION)));
	
	$response->setContent(file_get_contents($path));
	return $response;
	}


	/**
	 * @Template()
	 */	
	public function downloadFolderAction($id) {
	$em = $this -> getDoctrine() -> getManager();
	$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));
	$response = new Response();
	$response->headers->set('Content-type', 'application/octet-stream');
	if ($folder->getRealPath())
	    $source = $folder->getUploadRootDir().'/'.$folder->getRealPath().'/'.$folder->getPath();
	else
	    $source = $folder->getUploadRootDir().'/'.$folder->getPath();
	$destination = $source.'.zip';
	
	return $response;
	}
}
