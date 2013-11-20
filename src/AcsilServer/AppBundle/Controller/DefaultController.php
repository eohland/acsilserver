<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * This is the DefaultController which return the main page
 */
class DefaultController extends Controller {
	public function indexAction() {
		
		return $this -> redirect($this -> generateUrl('_acsil'));
	}

}
