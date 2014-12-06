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

//!  File and folder operations Webapp class. 
/*!
  Class of the Webapp. Perform the operations on the files and the folders
*/
class UploadController extends Controller {

 //! List the files and folders
  /*!
    \param $folderId the id of the current folder.
    \return an array containing the forms and a list of files and folders  
  */
	public function manageAction($folderId) {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$shareForm = $this -> createForm(new ShareFileType(), new ShareFile());
		$shareFolderForm = $this -> createForm(new ShareFileType(), new ShareFile());
		$renameForm = $this -> createForm(new RenameFileType(), new RenameFile());
		$form = $this -> createForm(new DocumentType(), new Document());
		$folderForm = $this -> createForm(new FolderType(), new Folder());
		$securityContext = $this -> get('security.context');
		
		$listAllfiles = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findBy(array('folder' => $folderId, 'isProfilePicture' => 0, 'owner' => $this->getUser()->getEmail()));



		$listusers = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findAll();
		
		$listAllFolders = $em
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findBy(array('parentFolder' => $folderId, 'owner' => $this->getUser()->getEmail()));

		if ($folderId == 0)
		{
			$parentId = 0;
			$query = $em->createQuery(
									'SELECT f
									FROM AcsilServerAppBundle:Folder f
									WHERE f.id > :folder AND f.isShared = 1 AND f.owner != :owner'
									)
									->setParameter('folder', 0)
									->setParameter('owner', $this->getUser()->getEmail());

			$sharedFolders = $query->getResult();
			$notDisplayed = array();
			foreach ($sharedFolders as $shared) {
				$parentSFolder = $em
					-> getRepository('AcsilServerAppBundle:Folder') 
					-> findOneBy(array('id' => $shared->getParentFolder()));
				if ($parentSFolder && $parentSFolder->getIsShared() == 1)
					array_push($notDisplayed, $shared);
			}
			foreach ($notDisplayed as $toDelete) {
				$key = array_search($toDelete, $sharedFolders);
				unset($sharedFolders[$key]);
			}	
			$listAllFolders = array_merge($listAllFolders, $sharedFolders);
		}
		else
		{
			$query = $em->createQuery(
									'SELECT f
									FROM AcsilServerAppBundle:Folder f
									WHERE f.parentFolder = :parentFolder AND f.isShared = 1 AND f.owner != :owner'
									)
									->setParameter('parentFolder', $folderId)
									->setParameter('owner', $this->getUser()->getEmail());
			$sharedFolders = $query->getResult();
			$listAllFolders = array_merge($listAllFolders, $sharedFolders);
		}
			
	/**
      * Get informations about folders
      */	
		$listfolders = array();
		foreach ($listAllFolders as $folder) {
		if ($securityContext -> isGranted('EDIT', $folder) === TRUE 
				|| $securityContext -> isGranted('VIEW', $folder) === TRUE) {
		
				$listUserFolderInfos = array();
				$sharedFolderUserInfos = array();
				if ($securityContext -> isGranted('OWNER', $folder) === TRUE) {
					foreach ($listusers as $user) {
						$aclProvider = $this -> container -> get('security.acl.provider');
						$objectIdentity = ObjectIdentity::fromDomainObject($folder);
						$acl = $aclProvider -> findAcl($objectIdentity);
						$securityContext = $this -> container -> get('security.context');
						$securityIdentity = UserSecurityIdentity::fromAccount($user);
						$aces = $acl -> getObjectAces();
						if ($user != $this -> getUser()) {
							$rights = NULL;
							foreach ($aces as $ace) {
								if ($ace -> getSecurityIdentity() == $securityIdentity) {
									if ($ace -> getMask() == MaskBuilder::MASK_VIEW) {
										$rights = "VIEW";
									}
									if ($ace -> getMask() == 13) {
										$rights = "EDIT";
									}
								}		
							}
							if ($rights != NULL)
								array_push($sharedFolderUserInfos, array("user" => $user, "rights" => $rights));
						}
					}
				}
				if (count($sharedFolderUserInfos) > 0)
					$listUserFolderInfos = array("info" => $folder, "sharedFolderUserInfos" => $sharedFolderUserInfos);
				else 
					$listUserFolderInfos = array("info" => $folder, "sharedFolderUserInfos" => '');
				array_push($listfolders, $listUserFolderInfos);
			}
		}			
			
		$currentPath = "";
		$parentIdList = array();
		if ($folderId == 0)
		{
			$parentId = 0;
			$query = $em->createQuery(
									'SELECT d
									FROM AcsilServerAppBundle:Document d
									WHERE d.isShared = 1 AND d.owner != :owner'
									)
									->setParameter('owner', $this->getUser()->getEmail());

			$sharedFiles = $query->getResult();
			$notDisplayed = array();
			foreach ($sharedFiles as $shared) {
				$parentSFolder = $em
					-> getRepository('AcsilServerAppBundle:Folder') 
					-> findOneBy(array('id' => $shared->getFolder()));
				if ($parentSFolder &&  $parentSFolder->getIsShared() == 1)
					array_push($notDisplayed, $shared);
			}		
			foreach ($notDisplayed as $toDelete) {
				$key = array_search($toDelete, $sharedFiles);
				unset($sharedFiles[$key]);
			}	
			$listAllfiles = array_merge($listAllfiles, $sharedFiles);
		}
		else
		{
			$query = $em->createQuery(
									'SELECT d
									FROM AcsilServerAppBundle:Document d
									WHERE d.folder = :folder AND d.isShared = 1 AND d.owner != :owner'
									)
									->setParameter('folder', $folderId)
									->setParameter('owner', $this->getUser()->getEmail());
			$sharedFiles = $query->getResult();
			$listAllfiles = array_merge($listAllfiles, $sharedFiles);

			$currentFolder = $em
				-> getRepository('AcsilServerAppBundle:Folder') 
				-> findOneBy(array('id' => $folderId));
			if ($currentFolder->getOwner() != $this->getUser()->getEmail())
			{
				$parentIdList = array();
				$parentId = 0;
			}
			else
			{
				$parentId = $currentFolder->getParentFolder();
				$tmpPath = $currentFolder->getChosenPath().$currentFolder->getName();
				$currentPath = explode("/", $tmpPath);
				$currentPath = array_reverse($currentPath);
				$tempId = $folderId;
				foreach ($currentPath as $stepFolder) {
					if ($tempId != 0) {
						$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
						if (!$parent) {
							throw $this->createNotFoundException('No parent found for id : '.$id);
						}
						$parentIdList[$tempId] = $stepFolder;
						$tempId = $parent->getParentFolder();
					}
				}	
				$parentIdList = array_reverse($parentIdList, true);
			}
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
								if ($ace -> getSecurityIdentity() == $securityIdentity) {
									if ($ace -> getMask() == MaskBuilder::MASK_VIEW) {
										$rights = "VIEW";
									}
									if ($ace -> getMask() == 13) {
										$rights = "EDIT";
									}
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
				'shareFolderForm' => $shareFolderForm -> createView(),
				'folderform' => $folderForm -> createView(),
				'renameForm' => $renameForm -> createView(),
			));
	}

 //! Upload a file
  /*!
    \param $folderId the id of the current folder.
    \return $folderId the id of the current folder.
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
		$document -> setLastModifDate(new \DateTime());
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

 //! Share a file
  /*!
    \param $request the request containing the data sent to the controller.
    \param $id the id of the file to share.
    \return $folderId the id of the current folder.
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
			$errorShare = 'No friends';
			$shareForm = $this -> createForm(new ShareFileType(), new ShareFile());
			//throw $this -> createNotFoundException('No user found for name ' . $friendName);
			return $this -> render('AcsilServerAppBundle:Upload:shareFile.html.twig',
			array(
				'shareForm' => $shareForm -> createView(),
				'errorShare' => $errorShare,
			));
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


	 //! Change the rights which have a user on a file
  /*!
    \param $fileId the id of the shared file.
    \param $userId the id of the user.
	\param $newRights the new rights that the user will have to perform actions on the file.
    \return $folderId the id of the current folder.
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
		$document -> setLastModifDate(new \DateTime());
		$em -> persist($document);
		$em -> flush();
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}

 //! Delete a file
  /*!
    \param $id the id of the file.
    \return $folderId the id of the current folder.
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
	
//! Rename a file
  /*!
    \param $id the id of the file to rename.
    \return $folderId the id of the current folder.
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
		$fileToRename -> setLastModifDate(new \DateTime());
		$em -> persist($fileToRename);
		$em -> flush();
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	
//! Rename a folder
  /*!
    \param $id the id of the folder to rename.
    \return $folderId the id of the current folder.
  */
	public function renameFolderAction($id) {
		$em = $this -> getDoctrine() -> getManager();
		$securityContext = $this -> get('security.context');
		$parameters = $_GET['acsilserver_appbundle_renamefiletype'];
		$name = $parameters['name'];

		$folderToRename = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneBy(array('id' => $id));
			
		if (!$folderToRename) {
			throw $this -> createNotFoundException('No folder found for id ' . $id);
		}
		
		$folderId = $folderToRename->getParentFolder();

		if (false === $securityContext -> isGranted('EDIT', $folderToRename)) {
			throw new AccessDeniedException();
		}
		$folderToRename->setName($name);
		$em -> persist($folderToRename);
		$em -> flush();
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	
	//! Create a folder
  /*!
    \param $folderId the id of the current folder.
    \return $folderId the id of the current folder.
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
		$folder -> setFSize(0);
		$folder -> setIsShared(0);		
		$tempId = $folderId;
		$totalPath = "";
		$chosenPath = "";
		if ($folderId != 0)
		{
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$parent->setFSize($parent->getFSize() + 1);
		$em->persist($parent);
		}
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
	
	//! Download a file
  /*!
    \param $id the id of the file to download.
    \return $response the response which contain the file.
  */
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

	//! Download a folder
  /*!
    \param $id the id of the folder to download.
    \return $response the response which contain an archive of the folder.
  */
	/**
	 * @Template()
	 */	
	public function downloadFolderAction($id) {
	$em = $this -> getDoctrine() -> getManager();
	$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));

	if ($folder->getSize() == 0 && $folder->getFSize() == 0)
	{
	$folderId = $folder->getParentFolder();
	return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	
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
	$response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.zip"', $zip_file));
	$response->setContent(file_get_contents($zip_file));
	@unlink($zip_file);
	return $response;
	}

	//! Move a file
  /*!
    \param $id the id of the file to move.
    \param $action used to differentiate COPY from CUT.
    \return $folderId the id of the current folder.
  */
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
	$allToMove = $em 
			-> getRepository('AcsilServerAppBundle:MoveFile') 
			-> findAll();
	$folderId = $fileToMove->getFolder();
	foreach ($allToMove as $move) {
			if (strpos($fileToMove->getAbsolutePath(), $move->getPath()) !== false)
				{
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
				}
			}
	$move = new MoveFile();
	$move-> setName($fileToMove->getName());
	$move-> setAction($action);
	$move-> setFileId($id);
	$move->setIsFolder(0);
	$move-> setPath($fileToMove->getAbsolutePath());
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
			-> findBy(array('isFolder' => 0));
		$folderToPaste = $em 
			-> getRepository('AcsilServerAppBundle:MoveFile') 
			-> findBy(array('isFolder' => 1));
			$currentFolder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $folderId));

//Folder Part
			
			//If the current folder is the root folder
			if ($folderId == 0)
			{
			$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
			//$rootpath = path of the folder's parent
		$rootPath = null;
			}
			$temp = new Folder();
			$subList = array();
		foreach ($folderToPaste as $move) {
		$subList = null;
		//cut
			if ($move->getAction() == 1) {			
			if ($folderId == 0)
			{
					if ($rootPath == null)
			{
			$rootPath = strstr($move->getPath(), basename($user->getUsername()), true).$user->getUsername();
			}
//			chmod();
			//Move folder
			rename($move->getPath(),$rootPath.'/'.basename($move->getPath()));
			//Get sub folders and files
			$subList = $temp->listDirectory($rootPath.'/'.basename($move->getPath()));
			}
			else
			{
//			chmod();
			rename($move->getPath(), $currentFolder->getAbsolutePath().'/'.basename($move->getPath()));
			$subList = $temp->listDirectory($currentFolder->getAbsolutePath().'/'.basename($move->getPath()));
			}
			$baseFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($move->getFileId());
$baseFolder->setParentFolder($folderId);

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
		$currentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$baseFolder -> setRealPath($totalPath);
		$baseFolder -> setChosenPath($chosenPath);
		$em -> persist($baseFolder);

			foreach ($subList as $sub) {
			$subname = basename($sub);
			// check if it's a folder or a file
			if ($subname[0] == 'd')
			{
					$currentSub = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('path' => $subname));
			}			
			else
			{
					$currentSub = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('path' => basename($sub)));
			$currentSub -> setLastModifDate(new \DateTime());
			}
			//Update the path
			$cS = $currentSub->getPath();
		if ($cS[0] == 'd')
		$tempId = $currentSub->getId();
		else
		$tempId = $currentSub->getFolder();
		$totalPath = "";
		$chosenPath = "";
		$baseId = $baseFolder->getId();
		while ($tempId != $baseId && $tempId != 0) {
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
		$currentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$totalPath = $baseFolder->getRealPath().'/'.$baseFolder->getPath().'/'.$totalPath;
		$chosenPath = $baseFolder->getChosenPath().'/'.$baseFolder->getName().'/'.$chosenPath;		
		$currentSub -> setRealPath($totalPath);
		$currentSub -> setChosenPath($chosenPath);
		$em -> persist($currentSub);
		$em->remove($move);
			}
			}
			//copy
			else {
			
			if ($folderId == 0)
			{
			
			if ($rootPath == null)
			{
			$rootPath = strstr($move->getPath(), basename($user->getUsername()), true).$user->getUsername();
			}	
			$temp->recurse_copy($move->getPath(), $rootPath.'/'.basename($move->getPath()));
			$subList = $temp->listDirectory($rootPath.'/'.basename($move->getPath()));
			}
			else
			{
			$temp->recurse_copy($move->getPath(), $currentFolder->getAbsolutePath().'/'.basename($move->getPath()));
			$subList = $temp->listDirectory($currentFolder->getAbsolutePath().'/'.basename($move->getPath()));
			}
			
			
						$baseFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($move->getFileId());
						$clonedBaseFolder = clone $baseFolder;
$clonedBaseFolder->setParentFolder($folderId);
		$em -> persist($clonedBaseFolder);
		$em -> flush();

				$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($clonedBaseFolder);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);

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
		$currentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$clonedBaseFolder -> setRealPath($totalPath);
		$clonedBaseFolder -> setChosenPath($chosenPath);
		$em -> persist($clonedBaseFolder);

			foreach ($subList as $sub) {
			$subname = basename($sub);
			// check if it's a folder or a file
			if ($subname[0] == 'd')
			{
					$currentSub = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('path' => $subname));
			}			
			else
			{
					$currentSub = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('path' => basename($sub)));
			$currentSub -> setLastModifDate(new \DateTime());
			}
			$clonedCurrentSub = clone $currentSub;
			//Update the path
		$cCS = $clonedCurrentSub->getPath();
		if ($cCS[0] == 'd')
		$tempId = $currentSub->getId();
		else
		$tempId = $currentSub->getFolder();
		$totalPath = "";
		$chosenPath = "";
		$baseId = $baseFolder->getId();
		while ($tempId != $baseId && $tempId != 0) {
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
		$currentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$totalPath = $clonedBaseFolder->getRealPath().'/'.$clonedBaseFolder->getPath().'/'.$totalPath;
		$chosenPath = $clonedBaseFolder->getChosenPath().'/'.$clonedBaseFolder->getName().'/'.$chosenPath;		
		$clonedCurrentSub -> setRealPath($totalPath);
		$clonedCurrentSub -> setChosenPath($chosenPath);

				if ($clonedCurrentSub->getPath() == 'd')
				$clonedCurrentSub -> setParentFolder($clonedBaseFolder->getId());
				else
				$clonedCurrentSub -> setFolder($clonedBaseFolder->getId());
		$clonedCurrentSub ->setPath = null;
		$em -> persist($clonedCurrentSub);
		$em -> flush();

						$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($clonedCurrentSub);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);
		$em->remove($move);
			}
			
			
			}
			//die(print_r($subList));
			
			
			}
		$em -> flush();
		
		// File part
		
			foreach ($filesToPaste as $move) {
			$newDoc = new Document();
			$newDoc -> setName($move->getName());
			$newDoc -> setIsProfilePicture(0);
			$newDoc -> setIsShared(0);
			$newDoc -> setOwner($this -> getUser() -> getEmail());
			$newDoc -> setuploadDate(new \DateTime());
			$newDoc -> setLastModifDate(new \DateTime());
			$newDoc -> setPseudoOwner($this -> getUser() -> getUsername());
			$newDoc -> setFolder($folderId);
			$origin = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('id' => $move->getFileId()));
			$newDoc->setSize($origin->getSize());
			$newDoc -> setMimeType($origin->getMimeType());
			$newDoc ->setFormatedSize($origin->getFormatedSize());
			$newDoc->setChosenPath($origin->getChosenPath());
			$file = $origin->getFile();
			$newDoc->setFile($origin->getFile());
			$tempPath = sha1(uniqid(mt_rand(), true));
			$endName = strstr($origin->getPath(), '.');
			$newDoc->setPath('f'.substr($tempPath, -6).$endName);



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
		$currentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$currentFolder->setSize($currentFolder->getSize() + 1);
		$em -> persist($currentFolder);
		}
		$newDoc -> setRealPath($totalPath);
		$newDoc -> setChosenPath($chosenPath);

	
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
        )));	}
	//! Delete a folder
  /*!
    \param $id the id of the folder to delete.
    \return $folderId the id of the current folder.
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
		unset($file_list[array_search($file,$file_list)]);
		
		}
		}
		$em -> flush();	
		$cpt = 1;
		while ($cpt != 0)
		{
		$cpt = 0;
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
		if ($folder->getFSize() == 0 && $folder->getSize() == 0)
{
		$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($folder);
		$aclProvider -> deleteAcl($objectIdentity);
		$parentId = $folder->getParentFolder();
		if ($parentId != 0)
		{
		$parentFolder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($parentId);
		$parentFolder->setFSize($parentFolder->getFSize() - 1);
		$em -> persist($parentFolder);
		}
		$em -> remove($folder);
		unset($file_list[array_search($file,$file_list)]);
		}
			else
		{
		$cpt = 1;
		}
		}
		else
		{
		unset($file_list[array_search($file,$file_list)]);
		}
		}
		}
		$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));
		$em -> remove($folder);
		$em -> flush();		

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	//! Move a file
  /*!
    \param $id the id of the folder to move.
    \param $action used to differentiate COPY from CUT.
    \return $folderId the id of the current folder.
  */
	/**
	 * @Template()
	 */		
	public function moveFolderAction($id, $action) {
	$em = $this -> getDoctrine() -> getManager();
		$folderToMove = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneBy(array('id' => $id));
		if (!$folderToMove) {
			throw $this -> createNotFoundException('No folder found for id ' . $id);
		}
		if ($action != 0 && $action != 1) {
			throw $this -> createNotFoundException('Unknown action: ' . $action);
		}
	$isExist = $em -> getRepository('AcsilServerAppBundle:MoveFile') -> findOneBy(array('fileId' => $id));
	if ($isExist)
	{
	$em -> remove($isExist);
	}
	$folderId = $folderToMove->getParentFolder();
		$allToMove = $em 
			-> getRepository('AcsilServerAppBundle:MoveFile') 
			-> findAll();
			foreach ($allToMove as $move) {
			if (strpos($folderToMove->getAbsolutePath(), $move->getPath()) !== false)
				{
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
				}
			if (strpos($move->getPath(), $folderToMove->getAbsolutePath()) !== false)
				{
				$em -> remove($move);
				}
			}
	$move = new MoveFile();
	$move-> setName($folderToMove->getName());
	$move-> setAction($action);
	$move-> setFileId($id);
	$move-> setPath($folderToMove->getAbsolutePath());
	$move->setIsFolder(1);
	$em -> persist($move);
	$em -> flush();
		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}

		//! Share a folder
  /*!
    \param $request the request containing the data sent to the controller.
    \param $id the id of the folder to share.
    \return $folderId the id of the current folder.
  */

	public function shareFolderAction(Request $request, $id) {
		
		$parameters = $request -> request -> get('acsilserver_appbundle_sharefiletype');
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
			$errorShare = 'No friends';
			$shareFolderForm = $this -> createForm(new ShareFileType(), new ShareFile());
			return $this -> render('AcsilServerAppBundle:Upload:shareFolder.html.twig',
				array(
					'shareFolderForm' => $shareFolderForm -> createView(),
					'errorShare' => $errorShare,
				));
		}

		$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));

		$folderId = $folder->getParentFolder();
		$file_list = $folder->listDirectory($folder->getAbsolutePath());
		$builder = new MaskBuilder();
		foreach ($file_list as $file) {
		$tmp = basename($file);
		if ($tmp[0] == 'f')
		{
			$document = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findOneBy(array('path' => $tmp));
					if (!$document) {
			throw $this -> createNotFoundException('No document found for path ' . $tmp);
		}
				/* file ----------------------------- */
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
		}
		}
		$em -> flush();

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
				/* folder---------------------------------- */
				if ($right == "EDIT") {
					$builder -> add('view') -> add('edit') -> add('delete');
					$folder->setIsShared(1);
				}
				if ($right == "VIEW") {
					$builder -> add('view');
					$folder->setIsShared(1);
				}
				/**
				* Set the rights for the other user 
				*/
				$aclProvider = $this -> container -> get('security.acl.provider');
				$objectIdentity = ObjectIdentity::fromDomainObject($folder);
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
					$folder->setIsShared(0);
				}
				$aclProvider -> updateAcl($acl);
				$em -> persist($folder);
				$em -> flush();		
			}
		}
		//last folder
		$folder = $em 
			-> getRepository('AcsilServerAppBundle:Folder') 
			-> findOneBy(array('id' => $id));
		if ($right == "EDIT") {
			$builder -> add('view') -> add('edit') -> add('delete');
			$folder->setIsShared(1);
			}
		if ($right == "VIEW") {
			$builder -> add('view');
			$folder->setIsShared(1);
		}
        /**
		 * Set the rights for the other user 
		*/
		$aclProvider = $this -> container -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($folder);
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
			$folder->setIsShared(0);
		}
		$aclProvider -> updateAcl($acl);
		$em -> persist($folder);
		$em -> flush();			

		return $this -> redirect($this -> generateUrl('_managefile', array(
            'folderId' => $folderId,
        )));
	}
	
	
}
