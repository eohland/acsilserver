<?php

namespace AcsilServer\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MonitoringControllerTest extends WebTestCase
{
    public function testOverview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/overview');
    }

}
