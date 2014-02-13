<?php

namespace AcsilServer\APIBundle\Controller;

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

use AcsilServer\APIBundle\Form\Type\DeleteType;
use AcsilServer\APIBundle\Entity\Delete;

class OperationsController extends Controller
{
    /**
     * @Rest\View()
     */
    public function copyAction(Request $request)
    {
        $copy = new Copy();

        $form = $this->createForm(new CopyType(), $copy);
        //$form->bind($request);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $response = new Response();
            //TODO: Perform copy action
            $response->setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }
    
    public function renameAction(Request $request)
    {
        $rename = new Rename();

        $form = $this->createForm(new RenameType(), $rename);
        //$form->bind($request);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $response = new Response();
            //TODO: Perform copy action
            $id = $form->get('fromId')->getData();
            $name = $form->get('toName')->getData();
            //$document= $this->container->get('doctrine.entity_manager')->getRepository('Document')->find($id);
            $em = $this -> getDoctrine() -> getManager();
            $document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id);
            $document->setName($name);
            $em -> persist($document);
            $em -> flush();
            $response->setContent($name."+".$id);
            $response->setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }

    public function moveAction()
    {
    }

    public function deleteAction()
    {
        $delete = new Delete();

        $form = $this->createForm(new DeleteType(), $delete);
        //$form->bind($request);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $response = new Response();
            //TODO: Perform copy action
            
            $id = $form->get('deleteId')->getData();
            
            //$securityContext = $this -> get('security.context');
            $em = $this -> getDoctrine() -> getManager();
            $fileToDelete = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneBy(array('id' => $id));
            /*if (false === $securityContext -> isGranted('DELETE', $fileToDelete)) {
                throw new AccessDeniedException();
            }*/
    
            $aclProvider = $this -> get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($fileToDelete);
            $aclProvider -> deleteAcl($objectIdentity);
            $em -> remove($fileToDelete);
            $em -> flush();
            
            $response->setContent($id);
            $response->setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }

}
