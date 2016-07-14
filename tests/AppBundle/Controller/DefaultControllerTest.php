<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
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
}
