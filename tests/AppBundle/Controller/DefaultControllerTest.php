<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;
    private $session = null;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->client = static::$kernel->getContainer()->get('test.client');
        $this->session = static::$kernel->getContainer()->get('session');
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    public function testHelloWorld()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hello');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello World', $crawler->filter('h1')->text());
    }


    public function testSend()
    {
        $client = static::createClient();

        $email = 'test@example.com';

        $crawler = $client->request('GET', '/send', [
            'email' => $email
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Sending an email to ' . $email . ' using sendmail', $crawler->filter('div')->text());


        $email = 'test2@example.com';

        $crawler = $client->request('GET', '/send', [
            'email' => $email
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Sending an email to ' . $email . ' using sendmail', $crawler->filter('div')->text());
    }

    public function testRegisterAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/user');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/login$/', $client->getResponse()->headers->get('location'));

        //now check that you can see the site if you are authenticated
        $this->logIn();
        $crawler = $this->client->request('GET', '/user');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    private function logIn()
    {
        $user = new User();
        $user->setEmail('admin@test.com');
        $user->setId(1);
        // the firewall context (defaults to the firewall name)
        $firewall = 'main';

        $token = new UsernamePasswordToken($user, '', $firewall, array('ROLE_USER'));
        $this->session->set('_security_' . $firewall, serialize($token));
        $this->session->save();

        $cookie = new Cookie($this->session->getName(), $this->session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}


