<?php

namespace AcsilServer\APIBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\DateTime;

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
use AcsilServer\AppBundle\Entity\ShareFile;
use AcsilServer\AppBundle\Form\ShareFileType;

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

  //! share a file.
  /*!
    \return The HTTP status code
  */
  public function shareAction($id) {
    $share = new Share();	//!< the data model for the form.

    $form = $this -> createForm(new ShareType(), $share);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $friendName = $form -> get('userMail') -> getData();	
      $rights = $form -> get('rights') -> getData();	
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
      $response -> setContent($id);
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


  //! List a folder.
  /*!
    \param $folderId.
    \return The HTTP status code
    \sa 
  */
  public function listAllFilesAction(Request $request) {
    $em = $this -> getDoctrine() -> getManager();
    $securityContext = $this -> get('security.context');

    $listAllfiles = $em 
      -> getRepository('AcsilServerAppBundle:Document') 
      -> findBy(array('isProfilePicture' => 0));


    $listusers = $em 
      -> getRepository('AcsilServerAppBundle:User') 
      -> findAll();
		
    $listfolders = $em
      -> getRepository('AcsilServerAppBundle:Folder') 
      -> findBy(array('owner' => $this->getUser()->getEmail()));

    $folderId = 0;
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
  public function uploadAction(Request $request, $folderId, $mobile) {
    /**
     * Create and fill a new document object
     */
    $response = new Response();
    $document = new Document();
    //$form->bind($request);
    $request = $this->getRequest();
    $name = $request->request->get('acsilserver_appbundle_documenttype');
    $response->setContent("name".$name['name']);

    $em = $this -> getDoctrine() -> getManager();
    $part_name = pathinfo($name['name']);
    $fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('folder' => $folderId, 'name' => $part_name['filename'] ));
    error_log( $name['name']);
    if ($fileToDelete != null) {
      //error_log( $fileToDelete->getName());
      $aclProvider = $this -> get('security.acl.provider');	//!< the file's acl rights.
      $objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
      $aclProvider -> deleteAcl($objectIdentity);
      $em -> remove($fileToDelete);
      $em -> flush();

    }

    $securityContext = $this -> get('security.context');
    $user = $securityContext -> getToken() -> getUser();
    $document -> setPseudoOwner($user -> getUsername());
	
    //$name = "test";
    if ($name['file'] != null && $name['name'] != null && $name['Content-Type'] != null) {
      $em = $this -> getDoctrine() -> getManager();
      $filename = $name['name'];
      $size = $name['Size'];
      $tempFilePath = $document->getUploadRootDir().'/'.$filename;
      if ($mobile == 0) {
	$fp = fopen($tempFilePath, "w+");
	fwrite($fp, $name['file']);
	fclose($fp);
      } else {
	file_put_contents($tempFilePath, base64_decode($name['file']));
      }
		
      $uploadedFile = new UploadedFile($tempFilePath, $filename, $name['Content-Type'], $size);
      $document -> setFile($uploadedFile);
      $infos = pathinfo($filename);
      $filename = basename($filename, '.'.$infos['extension']);
      $document->setMimeType($infos['extension']);
      $document -> setName($filename);
      $document -> setIsProfilePicture(0);
      $document -> setIsShared(0);
      $document ->setSize($size);
      $formatedSize = $document->formatSizeUnits(strval($size));
      $document->setFormatedSize($formatedSize);
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
      $document -> setLastModifDate(new \DateTime());
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
      //$document -> setSize(filesize($tempFilePath));
      $document->setFile(0);
      $em -> persist($document);
      if ($document->getRealPath())
	rename($tempFilePath, $document->getUploadRootDir().'/'.$document->getRealPath().'/'.$document->getPath());
      else
	rename($tempFilePath, $document->getUploadRootDir().'/'.$document->getPath());
      $em -> flush();
      /**
       * Set the rights
       */
      $aclProvider = $this -> get('security.acl.provider');
      $objectIdentity = ObjectIdentity::fromDomainObject($document);
      $acl = $aclProvider -> createAcl($objectIdentity);

      $securityIdentity = UserSecurityIdentity::fromAccount($user);

      $acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
      $aclProvider -> updateAcl($acl);

	       
      $response -> setContent($folderId);
      $response -> setStatusCode(201);
    } 
    return $response; 
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
	
  /**x
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
      $folder -> setLastModifDate(new \DateTime());
      $folder -> setPseudoOwner($this -> getUser() -> getUsername());
      $folder -> setParentFolder($folderId);
      $folder -> setIsShared(0);
      $folder -> setFSize(0);
      $folder -> setSize(0);
		
      $tempId = $folderId;
      $totalPath = "";
      $chosenPath = "";
      while ($tempId != 0) {
	$parent = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($tempId);
	if (!$parent) {
	  throw $this->createNotFoundException(
					       'No parent folder found for id : '.$tempId
					       );
	}
	$totalPath = $parent->getPath().'/'.$totalPath;
	$chosenPath = $parent->getName().'/'.$chosenPath;
	$tempId = $parent->getParentFolder();
      }
      $folder -> setChosenPath($chosenPath);
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
    return View::create($form, 400);
  }

  //! Delete a file.
  /*!
    \return The HTTP status code
  */
  public function deleteFolderAction($folderId) {
    $response = new Response(); 	//!< the response to the request.
    if ($folderId != 0) {
      

      $list = $this->listFilesAction($folderId);
      //      error_log(implode($list), 0);
      error_log(count($list['files']));
      for ($i = 0; $i != count($list['files']); $i++)
	{
	  error_log("files");
	  //	  error_log($list['files']);
	  error_log("files[i]");	
	  //	  error_log($list['files'][$i]);
	  error_log("files[i]info->getId");
	  error_log($list['files'][$i]['info']-> getId());
	  //$this -> deleteAction($list['files'][$i]['info']-> getId());
	  //$securityContext = $this -> get('security.context');
	  $em = $this -> getDoctrine() -> getManager();
	  $fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $list['files'][$i]['info']-> getId()));
	  /*if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
	    throw new AccessDeniedException();
	    }*/

	  $aclProvider = $this -> get('security.acl.provider');	//!< the file's acl rights.
	  $objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
	  $aclProvider -> deleteAcl($objectIdentity);
	  $em -> remove($fileToDelete);
	  $em -> flush();

	}
      for ($i = 0; $i != count($list['folders']); $i++)
	{
	  $this -> deleteFolderAction($list['folders'][$i]->getId());
	}

      $em = $this -> getDoctrine() -> getManager();
      $folderToDelete = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneBy(array('id' => $folderId));
      $aclProvider = $this -> get('security.acl.provider');	//!< the file's acl rights.
      $objectIdentity = ObjectIdentity::fromDomainObject($folderToDelete);
      $aclProvider -> deleteAcl($objectIdentity);
      $em -> remove($folderToDelete);
      $em -> flush();
      $response -> setContent($folderId);
      
      $response -> setStatusCode(201);
      return $response;
    }
    $response -> setStatusCode(400);
    return $response;
  }


  //! Rename a file.
  /*!
    \param $request the request containing the data sent to the API.
    \return The HTTP status code
    \sa moveAction(Request $request)
  */
  public function renameFolderAction(Request $request) {
    $rename = new Rename();	//!< the data model for the form.

    $form = $this -> createForm(new RenameType(), $rename);
    $form -> handleRequest($this -> getRequest());

    if ($form -> isValid()) {
      $response = new Response(); 	//!< the response to the request.
      $id = $form -> get('fromId') -> getData();	//!< the id of the file to rename.
      $name = $form -> get('toName') -> getData();	//!< the file's new name.
      $em = $this -> getDoctrine() -> getManager();
      $document = $em -> getRepository('AcsilServerAppBundle:Folder') -> findOneById($id);
      $document -> setName($name);
      $em -> persist($document);
      $em -> flush();
      $response -> setContent($name . "+" . $id);
      $response -> setStatusCode(201);
      return $response;
    }
    return View::create($form, 400);
  }
}