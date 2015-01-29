<?php
namespace AcsilServer\AppBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\APIBundle\Controller\OperationsController;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadListener {
	public function __construct() {
		$this->apiOptCtrl = new OperationsController();
	}

	public function onUpload(PostPersistEvent $event) {
	$folderId = 0; // Ou recup le folderId?
	
	$request = $event->getRequest();
	  $uploadedFile = $event->getFile();
			$document = new Document();
		$document -> setFile($uploadedFile);
		$filename = $request->get('name');
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
		//$document -> setOwner($this -> getUser() -> getEmail());
		$document -> setuploadDate(new \DateTime());
		$document -> setLastModifDate(new \DateTime());
		//$document -> setPseudoOwner($this -> getUser() -> getUsername());
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
		$folder->setLastModifDate(new \DateTime());
		$em -> persist($folder);
		}
		$document -> setRealPath($totalPath);
		$document -> setChosenPath($chosenPath);		
		/*$em -> persist($document);
		$em -> flush();
    /**
    * Set the rights
    */
	/*$aclProvider = $this -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> createAcl($objectIdentity);

		$securityContext = $this -> get('security.context');
		$user = $securityContext -> getToken() -> getUser();
		$securityIdentity = UserSecurityIdentity::fromAccount($user);

		$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
		$aclProvider -> updateAcl($acl);*/

	  }
}
?>
