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
use FOS\OAuthServerBundle\Storage\OAuthStorage;
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
    $clientManager = $this->get('fos_oauth_server.client_manager.default');
    
    $storage = $this->get('fos_oauth_server.storage');
    
    $clientManager = $this->get('fos_oauth_server.client_manager.default');
    //var_dump($clientManager);
    $client = $clientManager->findClientBy(array('id' => 1));
    //$client = $storage->getClient(1);
    //print_r('<pre>');
    //var_dump($clientManager);
    //print_r('</pre>');
    $user = $this -> getUser();
    
    $token = $storage->createAccessToken($this->genAccessToken(), $client, $user, 1, 'foo bar');
    return $this->render('AcsilServerAppBundle:Acsil:module.html.twig',
			 array(
			       'token' => $token->getToken(),
			       ));
    
			
  }
  protected function genAccessToken() {
    if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
      $randomData = file_get_contents('/dev/urandom', false, null, 0, 100);
    } elseif (function_exists('openssl_random_pseudo_bytes')) { // Get 100 bytes of pseudo-random data
      $bytes = openssl_random_pseudo_bytes(100, $strong);
      if (true === $strong && false !== $bytes) {
        $randomData = $bytes;
      }
    }
    // Last resort: mt_rand
    if (empty($randomData)) { // Get 108 bytes of (pseudo-random, insecure) data
      $randomData = mt_rand().mt_rand().mt_rand().uniqid(mt_rand(), true).microtime(true).uniqid(mt_rand(), true);
    }
    return rtrim(strtr(base64_encode(hash('sha256', $randomData)), '+/', '-_'), '=');
  }
}
