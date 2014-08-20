<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use AcsilServer\AppBundle\Entity\Folder;
use AcsilServer\AppBundle\Entity\ShareFile;
use AcsilServer\AppBundle\Entity\RenameFile;
use AcsilServer\AppBundle\Entity\MoveFile;
use AcsilServer\AppBundle\Form\ShareFileType;
use AcsilServer\AppBundle\Form\RenameFileType;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
		$renameForm = $this -> createForm(new RenameFileType(), new RenameFile());
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
	$currentPath = "";
	$parentIdList = array();
			if ($folderId == 0)
		{
		$parentId = 0;
			$query = $em->createQuery(
    'SELECT d
    FROM AcsilServerAppBundle:Document d
    WHERE d.folder > :folder AND d.isShared = 1 AND d.owner != :owner'
)
->setParameter('folder', 0)
->setParameter('owner', $this->getUser()->getEmail());

$sharedFiles = $query->getResult();
		$listAllfiles = array_merge($listAllfiles, $sharedFiles);
		}
		else
		{
		$currentFolder = $em
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $folderId, 'owner' => $this->getUser()->getEmail()));
		$parentId = $currentFolder->getParentFolder();
		$tmpPath = $currentFolder->getChosenPath().$currentFolder->getName();
		$currentPath = explode("/", $tmpPath);
		$currentPath = array_reverse($currentPath);
		$tempId = $folderId;
	foreach ($currentPath as $stepFolder) {
			if ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent found for id : '.$id
        );
		}
		$parentIdList[$tempId] = $stepFolder;
		$tempId = $parent->getParentFolder();
		}
		}	
		$parentIdList = array_reverse($parentIdList, true);
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
				'parentIdList' => $parentIdList,				
				'form' => $form -> createView(), 
				'shareForm' => $shareForm -> createView(),
				'folderform' => $folderForm -> createView(),
				'renameForm' => $renameForm -> createView(),
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
		$document -> setIsShared(0);
		if ($document -> getFile() == null) {
		return $this -> redirect($this -> generateUrl('_managefile', array(
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
		$chosenPath = "";
		while ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent found for id : '.$id
        );
		}
		$totalPath = $parent->getPath().'/'.$totalPath;
		$chosenPath = $parent->getName().'/'.$chosenPath;
		$tempId = $parent->getParentFolder();
		}
		if ($folderId != 0)
		{
		$folder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$folder->setSize($folder->getSize() + 1);
		$em -> persist($folder);
		}
		$document -> setRealPath($totalPath);
		$document -> setChosenPath($chosenPath);		
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
			$document->setIsShared(1);
			}
		if ($right == "VIEW") {
			$builder -> add('view');
			$document->setIsShared(1);
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
	else
	{
			$document->setIsShared(0);
	}
		$aclProvider -> updateAcl($acl);
		$em -> persist($document);
		$em -> flush();
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
			$document->setIsShared(1);
		}
		if ($newRights == "VIEW") {
			$builder -> add('view');
			$document->setIsShared(1);
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
			else
			{
			$document->setIsShared(0);			
			}
		$aclProvider -> updateAcl($acl);
		$em -> persist($document);
		$em -> flush();
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
		if ($folderId != 0)
		{
		$folder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$folder->setSize($folder->getSize() - 1);
		$em -> persist($folder);
		}
		$em -> remove($fileToDelete);
		$em -> flush();

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
/**
 * Rename a file
*/
	public function renameAction($id) {
		$em = $this -> getDoctrine() -> getManager();
		$securityContext = $this -> get('security.context');
		$parameters = $_GET['acsilserver_appbundle_renamefiletype'];
		$name = $parameters['name'];
		$fileToRename = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
		if (!$fileToRename) {
			throw $this -> createNotFoundException('No document found for id ' . $id);
		}
		$folderId = $fileToRename->getFolder();
		if (false === $securityContext -> isGranted('EDIT', $fileToRename)) {
			throw new AccessDeniedException();
		}
		$fileToRename->setName($name);
		$em -> persist($fileToRename);
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
		$folder -> setSize(0);
		
		$tempId = $folderId;
		$totalPath = "";
		$chosenPath = "";
		while ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent found for id : '.$tempId
        );
		}
		$totalPath = $parent->getPath().'/'.$totalPath;
		$chosenPath = $parent->getName().'/'.$chosenPath;
		$tempId = $parent->getParentFolder();
		}
		
		$folder-> setRealPath($totalPath);
		$folder-> setChosenPath($chosenPath);
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

	
$source_dir = $folder->getAbsolutePath();
$zip_file =  $folder->getName();
$file_list = $folder->listDirectory($folder->getAbsolutePath());
$zip = new \ZipArchive();
if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
  foreach ($file_list as $file) {
    if ($file !== $zip_file) {
	  $tmp = basename($file);
	  if ($tmp[0] == 'f')
	  {
	  	$doc = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('path' => $tmp));
					if (!$doc) {
			throw $this -> createNotFoundException('No document found for path ' . $tmp);
		}
		   $zip->addFromString($doc->getChosenPath().$doc->getName().'.'.$doc->getMimeType(), file_get_contents($file));
	}		
	}
	else
	throw $this -> createNotFoundException('No file found for ' . $file);
  }
  if ($zip->close() == false)
	throw $this -> createNotFoundException('Cannot create Zip file ' . $zip_file);
  }
	$response = new Response();
	$response->headers->set('Content-type', 'application/zip');
	$response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $zip_file));
	$response->setContent(file_get_contents($zip_file));
	@unlink($zip_file);
	return $response;
	}

	/**
	 * @Template()
	 */		
	public function moveAction($id, $action) {
	$em = $this -> getDoctrine() -> getManager();
		$fileToMove = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
		if (!$fileToMove) {
			throw $this -> createNotFoundException('No document found for id ' . $id);
		}
		if ($action != 0 && $action != 1) {
			throw $this -> createNotFoundException('Unknown action: ' . $action);
		}
	$isExist = $em -> getRepository('AcsilServerAppBundle:MoveFile') -> findOneBy(array('fileId' => $id));
	if ($isExist)
	{
	$em -> remove($isExist);
	}
	$move = new MoveFile();
	$move-> setName($fileToMove->getName());
	$move-> setAction($action);
	$move-> setFileId($id);
	$move-> setPath($fileToMove->getAbsolutePath());
	$folderId = $fileToMove->getFolder();
	$em -> persist($move);
	$em -> flush();
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
		/**
	 * @Template()
	 */		
	public function pasteAction($folderId) {
	$em = $this -> getDoctrine() -> getManager();
		$filesToPaste = $em 
			-> getRepository('AcsilServerAppBundle:MoveFile') 
			-> findAll();
			$currentFolder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $folderId));
			foreach ($filesToPaste as $move) {

			$newDoc = new Document();
			$newDoc -> setName($move->getName());
			$newDoc -> setIsProfilePicture(0);
			$newDoc -> setIsShared(0);
			$newDoc -> setOwner($this -> getUser() -> getEmail());
			$newDoc -> setuploadDate(new \DateTime());
			$newDoc -> setPseudoOwner($this -> getUser() -> getUsername());
			$newDoc -> setFolder($folderId);
			$origin = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('id' => $move->getFileId()));
			$newDoc->setSize($origin->getSize());
			$file = $origin->getFile();
			$newDoc->setFile($origin->getFile());
			$tempPath = sha1(uniqid(mt_rand(), true));
			//$endName = "jpeg";
			$endName = strstr($origin->getPath(), '.');
			$newDoc->setPath(substr($tempPath, -6).$endName);
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
			if ($folderId != 0)
			{
			$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$newDoc -> setRealPath($totalPath);

		

	
		copy($origin->getAbsolutePath(), $newDoc->getAbsolutePath());
		//cut
		
			if ($move->getAction() == 1) {
			if ($origin->getFolder() != 0)
			{
			$oldFolder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $origin->getFolder()));
			$oldFolder->setSize($oldFolder->getSize() - 1);
		$em -> persist($oldFolder);
		}
		$em -> remove($origin);
		}
		$em -> persist($newDoc);
		$em -> remove($move);
		$em -> flush();
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($newDoc);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);

		}
			
			return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
		/**
 * Delete a folder
*/
	public function deleteFolderAction($id) {
	
	
	
	$em = $this -> getDoctrine() -> getManager();
	$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));

	$folderId = $folder->getParentFolder();
$file_list = $folder->listDirectory($folder->getAbsolutePath());
  foreach ($file_list as $file) {
	  $tmp = basename($file);
	  if ($tmp[0] == 'f')
	  {
	  $doc = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('path' => $tmp));
					if (!$doc) {
			throw $this -> createNotFoundException('No document found for path ' . $tmp);
		}
	//	die(print_r(var_dump($doc)));
	//delete
//			if (false === $securityContext -> isGranted('DELETE', $doc)) {
//			throw new AccessDeniedException();
//		}
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($doc);
		$aclProvider -> deleteAcl($objectIdentity);
		$parentId = $doc->getFolder();
		if ($parentId != 0)
		{
		$folder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($parentId);
		$folder->setSize($folder->getSize() - 1);
		$em -> persist($folder);
		}
		$em -> remove($doc);
		
		}
		}
		 foreach ($file_list as $file) {
	  $tmp = basename($file);
			  if ($tmp[0] == 'd')
	  {
	  $folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('path' => $tmp));
					if (!$folder) {
			throw $this -> createNotFoundException('No folder found for path ' . $tmp);
		}
	//	die(print_r(var_dump($doc)));
	//delete
//			if (false === $securityContext -> isGranted('DELETE', $doc)) {
//			throw new AccessDeniedException();
//		}
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($folder);
		$aclProvider -> deleteAcl($objectIdentity);
		$em -> remove($folder);
		
		}	
		
		}
		
		$em -> flush();		

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
}
