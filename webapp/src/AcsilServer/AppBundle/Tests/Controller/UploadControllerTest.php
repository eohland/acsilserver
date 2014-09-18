<?php

namespace AcsilServer\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SecurityControllerTest extends WebTestCase
{
    protected $file;
    protected $image;

    public function testIndex()
    {
	
        $client = static::createClient();
        
		/* Login */
		
		$crawler = $client->request('GET', '/login/');
		$form = $crawler->selectButton('login-btn')->form();
		$form['_username'] = "test@test.fr";
		$form['_password'] = "test42";
		$crawler = $client->submit($form);
		$crawler = $client->followRedirect();		
		$crawler = $client->request('GET', '/acsil/');
		$this->assertTrue($client->getResponse()->isSuccessful());
		
		/* Upload file */
		
		$crawler = $client->request('GET', '/acsil/myfile/0');
		$this->assertTrue($client->getResponse()->isSuccessful());
		
		$crawler = $client->request('GET', '/acsil/myfile/0#addFile');
		$form = $crawler->selectButton('Upload')->form();
		$this->file = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(10, 10), $this->file);
        $this->image = new UploadedFile(
            $this->file,
            'new_image.png'
        );
		$form['acsilserver_appbundle_documenttype[name]'] = 'uploadTestAcsil';
		$form['acsilserver_appbundle_documenttype[file]'] = $this->image;
		$crawler = $client->submit($form);
		//$crawler = $client->followRedirect();
		$crawler = $client->request('GET', '/acsil/myfile/0');
		$this->assertTrue($crawler->filter('html:contains("uploadTestAcsil")')->count() > 0);
	    print "\nFile uploaded.\n";
		
		/* Logout */
		
		$crawler = $client->request('GET', '/acsil/');
		$link = $crawler->selectLink('Logout')->link();
		$crawler = $client->click($link);
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("Login")')->count() > 0);
		}
}
