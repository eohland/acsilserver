<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use AcsilServer\AppBundle\Entity\Folder;
use AcsilServer\AppBundle\Entity\ShareFile;
use AcsilServer\AppBundle\Entity\RenameFile;
use AcsilServer\AppBundle\Entity\MoveFile;
use AcsilServer\AppBundle\Entity\Module;
use AcsilServer\AppBundle\Form\ShareFileType;
use AcsilServer\AppBundle\Form\RenameFileType;
use AcsilServer\AppBundle\Form\DocumentType;
use AcsilServer\AppBundle\Form\FolderType;
use AcsilServer\AppBundle\Form\ModuleType;
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
use FOS\OAuthServerBundle\Storage\OAuthStorage;

//!  Modules operations Webapp class. 
/*!
  Class of the Webapp. Perform the operations on modules
*/
class ModuleController extends Controller {

 
	public function manageModuleAction($moduleId) {
	
	
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
    
    $token = null; //$storage->createAccessToken($this->genAccessToken(), $client, $user, 1, 'foo bar');
	
	
	
	
	
	$em = $this -> getDoctrine() -> getManager();
	$form = $this -> createForm(new ModuleType(), new Module());
$listAllModules = $em 
			-> getRepository('AcsilServerAppBundle:Module') 
			-> findAll();
			$currentCode = null;
	if ($moduleId > 0)
			{
			$execModule = $em 
			-> getRepository('AcsilServerAppBundle:Module') 
			-> findOneBy(array('id' => $moduleId));
			$currentCode = $execModule -> getCode();
			$currentCode = str_replace("<q>","'", $currentCode);
			$currentCode = htmlspecialchars_decode($currentCode);
		
			}
		
					return $this -> render('AcsilServerAppBundle:Acsil:module.html.twig',
			array(
				'form' => $form -> createView(),
				'moduleId' => $moduleId,
				'currentCode' => $currentCode,
				'listAllModules' => $listAllModules,
				//'token' => $token->getToken(),
			));
			
	}


	
		/**
	 * @Template()
	 */
	public function uploadModuleAction() {
		$em = $this -> getDoctrine() -> getManager();
    /**
     * Create and fill a new document object
    */
		$module = new Module();
		$request = $this -> getRequest();
		$uploadedFile = $request -> files -> get('acsilserver_appbundle_moduletype');
		$parameters = $request -> request -> get('acsilserver_appbundle_moduletype');
		$file = $uploadedFile['file'];
		$filename = $parameters['name'];
		$code = file_get_contents($file->getPathName(), FILE_USE_INCLUDE_PATH);
		 $code = htmlspecialchars($code);
  $code = str_replace("'","<q>",$code);

		$module -> setCode($code);
		$module -> setName($filename);
		if ($file == null) {
		return $this -> redirect($this -> generateUrl('_module'));
		}
		if ($module -> getName() == null) {
			$module -> setName($file -> getClientOriginalName());
		}
			
		$em -> persist($module);
		$em -> flush();
		return $this -> redirect($this -> generateUrl('_module'));
	}

	
	
		
		/**
	 * @Template()
	 */
	public function deleteModuleAction($moduleId) {
		$em = $this -> getDoctrine() -> getManager();
if ($moduleId > 0)
			{
			$moduleToDelete = $em 
			-> getRepository('AcsilServerAppBundle:Module') 
			-> findOneBy(array('id' => $moduleId));

			$em -> remove($moduleToDelete);
		$em -> flush();
			}
	
	return $this -> redirect($this -> generateUrl('_module'));
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
