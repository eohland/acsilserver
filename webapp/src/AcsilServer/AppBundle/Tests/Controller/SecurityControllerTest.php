<?php

namespace AcsilServer\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadControllerTest extends WebTestCase
{
    protected $file;
    protected $image;

    public function testIndex()
    {
	
        $client = static::createClient();
        
		/* Create first user */
		
		$crawler = $client->request('GET', '/login/');
		if ($crawler->filter('html:contains("Welcome admin")')->count() > 0)
		{
		$form = $crawler->selectButton('submit')->form();
		$form['acsilserver_appbundle_usertype[firstname]'] = 'Test';
		$form['acsilserver_appbundle_usertype[lastname]'] = 'TEST';
		$form['acsilserver_appbundle_usertype[email]'] = 'test@test.fr';
		$form['acsilserver_appbundle_usertype[password]'] = 'test42';
		$form['acsilserver_appbundle_usertype[confirm_password]'] = 'test42';
		$this->file = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(10, 10), $this->file);
        $this->image = new UploadedFile(
            $this->file,
            'new_image.png'
        );
		$form['acsilserver_appbundle_usertype[pictureAccount]'] = $this->image;
		$crawler = $client->submit($form);
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("Login")')->count() > 0);
		print "\nAdmin created.\n";
		}
		
		/* Login */
		
		$form = $crawler->selectButton('login')->form();
		$form['_username'] = "test@test.fr";
		$form['_password'] = "test42";
		$crawler = $client->submit($form);
		$crawler = $client->followRedirect();		
		$crawler = $client->request('GET', '/acsil/');
		$this->assertTrue($client->getResponse()->isSuccessful());
        print "\nLogged in.\n";
		
		/* Create new user*/
		
		$crawler = $client->request('GET', '/acsil/admins/#newUser');
		$form = $crawler->selectButton('submit')->form();
		$form['acsilserver_appbundle_usertype[firstname]'] = 'Test2';
		$form['acsilserver_appbundle_usertype[lastname]'] = 'TEST2';
		$form['acsilserver_appbundle_usertype[email]'] = 'test2@test2.fr';
		$form['acsilserver_appbundle_usertype[password]'] = 'test42';
		$form['acsilserver_appbundle_usertype[confirm_password]'] = 'test42';
		$form['acsilserver_appbundle_usertype[usertype]'] = 'user';
		$this->file = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(10, 10), $this->file);
        $this->image = new UploadedFile(
            $this->file,
            'new_image.png'
        );
		$form['acsilserver_appbundle_usertype[pictureAccount]'] = $this->image;
		$crawler = $client->submit($form);
		$crawler = $client->request('GET', '/acsil/admins/');
		$this->assertTrue($crawler->filter('html:contains("TEST")')->count() > 0);
		print "User created.\n";
		
		/* Logout */
		
		$crawler = $client->request('GET', '/acsil/');
		$link = $crawler->selectLink('Logout')->link();
		$crawler = $client->click($link);
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("Login")')->count() > 0);
		print "Logged out.\n";
		
		}
		
}
