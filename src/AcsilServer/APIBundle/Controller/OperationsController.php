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

use AcsilServer\APIBundle\Form\Type\MoveType;
use AcsilServer\APIBundle\Entity\Move;

use AcsilServer\APIBundle\Form\Type\DeleteType;
use AcsilServer\APIBundle\Entity\Delete;

use AcsilServer\AppBundle\Entity\Document;

class OperationsController extends Controller {
    /**
     * @Rest\View()
     */
    public function copyAction(Request $request) {
        $copy = new Copy();

        $form = $this -> createForm(new CopyType(), $copy);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());

        if ($form -> isValid()) {
            $response = new Response();
            $id = $form -> get('fromId') -> getData();
            $path = $form -> get('toPath') -> getData();
            //$document= $this->container->get('doctrine.entity_manager')->getRepository('Document')->find($id);

            $ret = $this -> copyFile($id, $path, "copy", $response);

            /*$document->setName($name);
             $em -> persist($document);
             $em -> flush();*/
            $response -> setContent($ret);
            $response -> setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }

    public function renameAction(Request $request) {
        $rename = new Rename();

        $form = $this -> createForm(new RenameType(), $rename);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());

        if ($form -> isValid()) {
            $response = new Response();
            //TODO: Perform copy action
            $id = $form -> get('fromId') -> getData();
            $name = $form -> get('toName') -> getData();
            //$document= $this->container->get('doctrine.entity_manager')->getRepository('Document')->find($id);
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

    public function moveAction() {
        $move = new Move();

        $form = $this -> createForm(new MoveType(), $move);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());

        if ($form -> isValid()) {
            $response = new Response();
            //TODO: Perform copy action
            $id = $form -> get('fromId') -> getData();
            $path = $form -> get('toPath') -> getData();
            //$document= $this->container->get('doctrine.entity_manager')->getRepository('Document')->find($id);

            $ret = $this -> copyFile($id, $path, "move", $response);

            /*$document->setName($name);
             $em -> persist($document);
             $em -> flush();*/
            $response -> setContent($ret);
            $response -> setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }

    public function deleteAction() {
        $delete = new Delete();

        $form = $this -> createForm(new DeleteType(), $delete);
        //$form->bind($request);
        $form -> handleRequest($this -> getRequest());

        if ($form -> isValid()) {
            $response = new Response();
            //TODO: Perform copy action

            $id = $form -> get('deleteId') -> getData();

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

            $response -> setContent($id);
            $response -> setStatusCode(201);
            return $response;
        }
        return View::create($form, 400);
    }

    private function copyFile($id, $toPath, $action, $response) {
        $i = 1;
        $realPath = "";
        $parentFolder = 0;

        $em = $this -> getDoctrine() -> getManager();
        $document = $em -> getRepository('AcsilServerAppBundle:Document') -> findOneById($id);
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
            $newPath = $folder -> getAbsolutePath();
        } else {

            $newPath = $document -> getAbsolutePath();
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

}
