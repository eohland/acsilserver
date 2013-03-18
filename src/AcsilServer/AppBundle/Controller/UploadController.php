<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
/**
 * @Template()
 */
public function uploadAction()
{
    $document = new Document();
    $form = $this->createFormBuilder($document)
        ->add('file')
        ->getForm()
    ;
	
    if ($this->getRequest()->isMethod('POST')) {
        $form->bind($this->getRequest());
    if ($form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $document->setName($document->getFile()->getClientOriginalName());
	$document->setOwner($this->getUser()->getEmail());
	$document->setuploadDate(new \Datetime());
    $em->persist($document);
    $em->flush();
     $this->redirect('_acsil');
 
   }
    }

    return array('form' => $form->createView());
}

public function manageAction()
{
	$em = $this->getDoctrine()->getManager();
		$listfiles = $em
			->getRepository('AcsilServerAppBundle:Document')
			->findByOwner($this->getUser()->getEmail());
		return $this->render('AcsilServerAppBundle:Upload:files.html.twig',
			array(
				'listfiles' =>$listfiles,
			));
}

	public function deleteAction($id) {
		$em = $this->getDoctrine()->getManager();
		$fileToDelete = $em
			->getRepository('AcsilServerAppBundle:Document')
			->findOneBy(array('id' => $id));
		$em->remove($fileToDelete);
		$em->flush();
		
		return $this->redirect($this->generateUrl('_managefile'));
	}
}
