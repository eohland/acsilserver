<?php

namespace AcsilServer\APIBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

use AcsilServer\APIBundle\Form\Type\CopyType;
use AcsilServer\APIBundle\Entity\Copy;

use AcsilServer\APIBundle\Form\Type\RenameType;
use AcsilServer\APIBundle\Entity\Rename;

use AcsilServer\APIBundle\Form\Type\MoveType;
use AcsilServer\APIBundle\Entity\Move;

use AcsilServer\APIBundle\Form\Type\DeleteType;
use AcsilServer\APIBundle\Entity\Delete;

use AcsilServer\AppBundle\Entity\Document;
use AcsilServer\AppBundle\Form\DocumentType;
use AcsilServer\AppBundle\Entity\Folder;
use AcsilServer\AppBundle\Form\FolderType;


//!  File operations API class. 
/*!
  Class of the API. Perform the operations on the files
*/
class OperationsController extends Controller {
  /**
   * @Rest\View()
   */

  //! Copy a file.
  /*!
    \param $request the request containing the data sent to the API.
    \return The HTTP status code
    \sa moveAction(Request $request) and copyFile($is, $path, $action, $reponse)
  */
  public function copyAction(Request $request) {
    $copy = new Copy();	//!< the data model for the form.

    $form = $this -> createForm(new CopyType(), $copy);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $id = $form -> get('fromId') -> getData();	//!< the id of the file to copy.
      $path = $form -> get('toPath') -> getData();	//!< the file's destination path.
      $ret = $this -> copyFile($id, $path, "copy", $response); //!< the content of the response to the request
      $response -> setContent($ret);
      $response -> setStatusCode(201);
      return $response;
    }
    return View::create($form, 400);
  }

  //! Rename a file.
  /*!
    \param $request the request containing the data sent to the API.
    \return The HTTP status code
    \sa moveAction(Request $request)
  */
  public function renameAction(Request $request) {
    $rename = new Rename();	//!< the data model for the form.

    $form = $this -> createForm(new RenameType(), $rename);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $id = $form -> get('fromId') -> getData();	//!< the id of the file to rename.
      $name = $form -> get('toName') -> getData();	//!< the file's new name.
      $em = $this -> getDoctrine() -> getManager();
      $document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id);
      $document -> setName($name);
      $em -> persist($document);
      $em -> flush();
      $response -> setContent($name . "+" . $id);
      $response -> setStatusCode(201);
      return $response;
    }
    return View::create($form, 400);
  }

  //! Move a file.
  /*!
    \return The HTTP status code
    \sa renameAction(Request $request) and copyFile($is, $path, $action, $reponse)
  */
  public function moveAction() {
    $move = new Move();	//!< the data model for the form.

    $form = $this -> createForm(new MoveType(), $move);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $id = $form -> get('fromId') -> getData();	//!< the id of the file to move.
      $path = $form -> get('toPath') -> getData();	//!< the file's destination path.
      $ret = $this -> copyFile($id, $path, "move", $response); //!< the content of the response to the request
      $response -> setContent($ret);
      $response -> setStatusCode(201);
      return $response;
    }
    return View::create($form, 400);
  }

  //! Delete a file.
  /*!
    \return The HTTP status code
  */
  public function deleteAction() {
    $delete = new Delete();	//!< the data model for the form.

    $form = $this -> createForm(new DeleteType(), $delete);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $id = $form -> get('deleteId') -> getData(); 	//!< the id of the file to delete.

      //$securityContext = $this -> get('security.context');
      $em = $this -> getDoctrine() -> getManager();
      $fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
      /*if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
	throw new AccessDeniedException();
	}*/

      $aclProvider = $this -> get('security.acl.provider');	//!< the file's acl rights.
      $objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
      $aclProvider -> deleteAcl($objectIdentity);
      $em -> remove($fileToDelete);
      $em -> flush();

      $response -> setContent($id);
      $response -> setStatusCode(201);
      return $response;
    }
    return View::create($form, 400);
  }

  //! Perform the action on a file.
  /*!
    \param $id the id of the file to act on.
    \param $toPath the destination path.
    \param $action the action to perform.
    \param $response the reponse to set.
    \return $response set to the correct HTTP status code
    \sa moveAction(Request $request), renameAction(Request $request) and copyAction(Request $request).
  */
  private function copyFile($id, $toPath, $action, $response) {
    $i = 1;	//!< an index to parse the path
    $realPath = ""; 	//!< a sting to store the real path on hard drive.
    $parentFolder = 0;	//!< an integer to store the parent folder when browsing the hard drive.

    $em = $this -> getDoctrine() -> getManager();
    $document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id); 	//!< the file to copy/move.
    if ($toPath != "/") {
      $tabName = explode('/', $toPath);
      while ($i < count($tabName)) {
	$folder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneBy(array("parentFolder" => $parentFolder, "name" => $tabName[$i]));
	if ($folder == NULL) {
	  $response -> setStatusCode(400);
	  return $response;
	}
	$realPath .= $folder -> getPath() . "/";
	$parentFolder = $folder -> getId();
	$i++;
      }
      $newPath = $folder -> getAbsolutePath();	//!< the new location's absolute path.
    } else {

      $newPath = $document -> getAbsolutePath();	//!< the new location's absolute path.
      $newPath = substr($newPath, 0, strpos($newPath, $document -> getRealPath()));
    }
    if (copy($document -> getAbsolutePath(), $newPath . "/" . $document -> getPath()) == FALSE)
      return "FALSE";

    if ($action == "move") {
      $oldFilename = $document -> getAbsolutePath();
      if ($parentFolder != 0)
	$document -> setFolder(TRUE);
      $document -> setRealPath($realPath);
      $em -> persist($document);
      unlink($oldFilename);
    } else {
      $newDocument = new Document();
      if ($parentFolder != 0)
	$newDocument -> setFolder(TRUE);
      $newDocument -> setRealPath($realPath);
      $newDocument -> setPath($document -> getPath());
      $newDocument -> setIsProfilePicture($document -> getIsProfilePicture());
      $newDocument -> setSize($document -> getSize());
      $newDocument -> setName($document -> getname());
      $newDocument -> setOwner($document -> getOwner());
      $newDocument -> setuploadDate($document -> getUploadDate());
      $newDocument -> setPseudoOwner($document -> getPseudoOwner());

      $em -> persist($newDocument);

      /**
       * Set the rights
       */
      /*           $aclProvider = $this -> get('security.acl.provider');
		   $objectIdentity = ObjectIdentity::fromDomainObject($document);
		   $acl = $aclProvider -> createAcl($objectIdentity);

		   $securityContext = $this -> get('security.context');
		   $user = $securityContext -> getToken() -> getUser();
		   $securityIdentity = UserSecurityIdentity::fromAccount($user);

		   $acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		   $aclProvider -> updateAcl($acl);
      */

    }
    $em -> flush();
    return (">>>>" . $realPath . "+" . $id);
  }

  //! List a folder.
  /*!
    \param $folderId.
    \return The HTTP status code
    \sa 
  */
 public function listFilesAction($folderId) {
 $em = $this -> getDoctrine() -> getManager();
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

$list = array("files" => $listfiles, "folders" => $listfolders, "users" => $listusers);
	return ($list);
	}


	/**
	 * @Template()
	 */
	public function uploadAction(Request $request, $folderId) {
    /**
     * Create and fill a new document object
    */
	$document = new Document();
	$form = $this -> createForm(new DocumentType(), $document);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());

        if ($form -> isValid()) {
		$em = $this -> getDoctrine() -> getManager();
		$uploadedFile = $form -> get('file') -> getData();
		$filename = $form -> get('name') -> getData();
		$document -> setFile($uploadedFile);
		$document -> setName($filename);
		$document -> setIsProfilePicture(0);
		if ($document -> getFile() == null) {
       throw $this->createNotFoundException(
            'No file found'
        );		
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
		if ($folderId != 0)
		{
		$folder = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($folderId);
		$folder->setSize($folder->getSize() + 1);
		$em -> persist($folder);
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

		$response = new Response();
        $response -> setContent($folderId);
        $response -> setStatusCode(201);
        return $response; 
	}
		return View::create($form, 400);
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
	if ($id == 0)
	{
	 $response -> setContent($id);
	 $response -> setStatusCode(404);
	 return ($response);
	}
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
	public function folderAction(Request $request, $folderId) {
	$folder = new Folder();
	$form = $this -> createForm(new FolderType(), $folder);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());
        if ($form -> isValid()) {
		$em = $this -> getDoctrine() -> getManager();
		$foldername = $form -> get('name') -> getData();
		$folder -> setName($foldername);
		$folder -> setOwner($this -> getUser() -> getEmail());
		$folder -> setuploadDate(new \DateTime());
		$folder -> setPseudoOwner($this -> getUser() -> getUsername());
		$folder -> setParentFolder($folderId);
		$folder -> setSize(0);
		
		$tempId = $folderId;
		$totalPath = "";
		while ($tempId != 0) {
		$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
		   if (!$parent) {
        throw $this->createNotFoundException(
            'No parent folder found for id : '.$tempId
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

		$response = new Response();
        $response -> setContent($folderId);
        $response -> setStatusCode(201);
        return $response;
		}
	}
}