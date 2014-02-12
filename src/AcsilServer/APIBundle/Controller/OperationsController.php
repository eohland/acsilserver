<?php

namespace AcsilServer\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

use AcsilServer\APIBundle\Form\Type\CopyType;
use AcsilServer\APIBundle\Entity\Copy;

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

    public function moveAction()
    {
    }

    public function deleteAction()
    {
    }

}
