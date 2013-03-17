<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$session = $this->get('session');
    	$em = $this->getDoctrine()->getManager();
		$superAdmin = json_encode(array('ROLE_SUPER_ADMIN'));
		
		$isSuperAdmin = $em
			->getRepository('AcsilServerAppBundle:User')
			->findOneBy(array('roles' => $superAdmin));
		
		if ($isSuperAdmin)
			return $this->redirect($this->generateUrl('_acsil'));
		else {
			$session->setFlash('Notice', $this->get('translator')->trans('create.admin'));
			return $this->redirect($this->generateUrl('_register', array('userRole' => "superAdmin")));
		}
    }
}
