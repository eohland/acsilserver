<?php

namespace AcsilServer\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AcsilServer\AppBundle\Entity\Document;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\APIBundle\Form\Type\FileType;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function RenameAction(Document $document)
    {
	return $this->processForm($document);
    }

    private function processForm(Document $document)
    {
        /*$statusCode = $user->isNew() ? 201 : 204;*/

        $form = $this->createForm(new FileType(), $document);
		if ($request->isMethod('PUT')) {
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $Document->save();

            $response = new Response();
            $response->setStatusCode($statusCode);

           /* // set the `Location` header only when creating new resources
            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'acme_demo_user_get', array('id' => $user->getId()),
                        true // absolute
                    )
                );
            }*/

            return $response;
        }
		}
$response = new Response();

$response->setContent("form");
$response->setStatusCode(400);
$response->headers->set('Content-Type', 'text/html');

// prints the HTTP headers followed by the content
$response->send();
return $response;
     }

}
