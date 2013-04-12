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

class UploadController extends Controller {
	
	public function manageAction() {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$shareForm = $this->createForm(new ShareFileType(), new ShareFile());
		//$form = $this->createForm(new DocumentType(), new Document());
		$form = $this -> createFormBuilder($document)
		->add('name')
		->add('file')
		-> getForm();

		$listAllfiles = $em 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findBy(array('isProfilePicture' => 0));
		$listusers = $em 
			-> getRepository('AcsilServerAppBundle:User') 
			-> findAll();
		$securityContext = $this -> get('security.context');
		$listfiles = null;
		foreach ($listAllfiles as $file) {
			if (true === $securityContext -> isGranted('DELETE', $file) 
				|| true === $securityContext -> isGranted('EDIT', $file) 
				|| true === $securityContext -> isGranted('VIEW', $file)) {
				$listfiles[] = $file;
			}

		}

		return $this -> render('AcsilServerAppBundle:Acsil:files.html.twig', 
			array(
				'listfiles' => $listfiles, 
				'listusers' => $listusers, 
				'form' => $form->createView(),
				'shareForm' => $shareForm->createView(),
			));
	}

	/**
	 * @Template()
	 */
	public function uploadAction() {
		$em = $this -> getDoctrine() -> getManager();
//		$emm = $this -> getDoctrine() -> getManager();

		$document = new Document();
//		$form = $this->createForm(new DocumentType(), new Document());

		$form = $this -> createFormBuilder($document)
		->add('name')
		->add('file')
		-> getForm();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());
			if ($form -> isValid()) {
				$document -> setIsProfilePicture(0);
				if ($document -> getFile() == null) {
					return $this -> redirect($this -> generateUrl('_upload'));
				}
				if ($document->getName() == null)
				{
				$document -> setName($document -> getFile() -> getClientOriginalName());
				}
				$document -> setOwner($this -> getUser() -> getEmail());
				$document -> setuploadDate(new \DateTime());				
				
				
				
	/*						$listAllfiles = $emm 
			-> getRepository('AcsilServerAppBundle:Document') 
			-> findBy(array('isProfilePicture' => 0));
			$listfiles = null;
		foreach ($listAllfiles as $file) {
			if (true === $securityContext -> isGranted('EDIT', $file) && $file.getName() == $document -> getName()) {
				$listfiles[] = $file;
			}
			}
*/
				
				
				$em -> persist($document);
				$em -> flush();

				
				
				
				
				$aclProvider = $this -> get('security.acl.provider');
				$objectIdentity = ObjectIdentity::fromDomainObject($document);
				$acl = $aclProvider -> createAcl($objectIdentity);

				$securityContext = $this -> get('security.context');
				$user = $securityContext -> getToken() -> getUser();
				$securityIdentity = UserSecurityIdentity::fromAccount($user);

				$acl -> insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
				$aclProvider -> updateAcl($acl);
				
		/*		if ($listfiles != null)
				{
				return $this -> redirect($this -> generateUrl('_acsil'));
				}*/
					
				return $this -> redirect($this -> generateUrl('_managefile'));
			}
		}
			//return $this -> redirect($this -> generateUrl('_managefile'));
    return $this->render('AcsilServerAppBundle:Upload:upload.html.twig', array(
            'form' => $form->createView(),
			));
	}

public function shareAction(Request $request, $id) {
		$parameters = $request->request->get('acsilserver_appbundle_sharefiletype');
		$friendName = $parameters['userMail'];
		$right = $parameters['rights'];
		if ($friendName == NULL)
		{
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
		if ($right == "EDIT")
		{
		  $builder -> add('view') -> add('edit') -> add('delete');
		}
		else
		{
		 $builder -> add('view');
		}
		$mask = $builder -> get();
		$aclProvider = $this -> container -> get('security.acl.provider');
		$objectIdentity = ObjectIdentity::fromDomainObject($document);
		$acl = $aclProvider -> findAcl($objectIdentity);

		$securityContext = $this -> container -> get('security.context');
		$securityIdentity = UserSecurityIdentity::fromAccount($friend);

		var_dump($builder -> get());
		$acl -> insertObjectAce($securityIdentity, $mask);
		$aclProvider -> updateAcl($acl);
		return $this -> redirect($this -> generateUrl('_managefile'));
	}

	/**
	 * @Template()
	 */
	public function pictureAction() {
		$em = $this -> getDoctrine() -> getManager();
		$document = new Document();
		$form = $this -> createFormBuilder($document) -> add('file') -> getForm();

		if ($this -> getRequest() -> isMethod('POST')) {
			$form -> bind($this -> getRequest());
			if ($form -> isValid()) {
				if ($document -> getFile() == null) {
					return $this -> redirect($this -> generateUrl('_upload_picture'));
				}
				$picturePath = $this -> getUser() -> getPictureAccount();
				if (!empty($picturePath)) {
					$query = $em 
						-> createQuery('SELECT d FROM AcsilServerAppBundle:Document d WHERE d.name = :docName AND d.isProfilePicture = 1') 
						-> setParameter('docName', 'avatar-' . $this -> getUser() -> getEmail());
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

	public function deleteAction($id) {
		$securityContext = $this -> get('security.context');
		$em = $this -> getDoctrine() -> getManager();
		$fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
		if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
			throw new AccessDeniedException();
		}
		$em -> remove($fileToDelete);
		$em -> flush();

		return $this -> redirect($this -> generateUrl('_managefile'));
	}

}
