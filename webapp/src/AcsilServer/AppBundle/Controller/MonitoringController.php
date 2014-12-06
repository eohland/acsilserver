<?php

namespace AcsilServer\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MonitoringController extends Controller
{
    public function overviewAction()
    {
        return $this->render('AcsilServerAppBundle:Monitoring:overview.html.twig', array(
                // ...
            ));    }

}
