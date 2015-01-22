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

//!  Modules operations Webapp class. 
/*!
  Class of the Webapp. Perform the operations on modules
*/
class ModuleController extends Controller {

 
	public function manageModuleAction() {
	
	
			return $this->render('AcsilServerAppBundle:Acsil:module.html.twig');
			
	}


		
		public function createAccessToken($tokenString, IOAuth2Client $client, $data, $expires, $scope = null)
    {
        if (!$client instanceof ClientInterface) {
            throw new \InvalidArgumentException('Client has to implement the ClientInterface');
        }

        $token = $this->accessTokenManager->createToken();
        $token->setToken($tokenString);
        $token->setClient($client);
        $token->setExpiresAt($expires);
        $token->setScope($scope);

        if (null !== $data) {
            $token->setUser($data);
        }

        $this->accessTokenManager->updateToken($token);

     
			return $this -> redirect($this -> generateUrl('_module', array(
            'token' => $token,
        )));
    }
	
}
