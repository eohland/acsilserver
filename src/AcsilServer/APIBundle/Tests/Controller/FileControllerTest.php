<?php

namespace AcsilServer\APIBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FileControllerTest extends WebTestCase
{
    public function testRename()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Rename');
    }

}
