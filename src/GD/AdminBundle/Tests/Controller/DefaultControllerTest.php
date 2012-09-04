<?php

namespace GD\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/admin/dashboard');
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertRegExp('/Redirecting to .+login/', $client->getResponse()->getContent());
    }

    public function testLoginFormRendering()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/admin/dashboard');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('form')->count() == 1);
        $this->assertTrue($crawler->filter('html:contains("Username")')->count() == 1);
        $this->assertTrue($crawler->filter('html:contains("Password")')->count() == 1);
        $this->assertTrue($crawler->filter('html:contains("Remember me")')->count() == 1);
    }

    public function testLoginFailure()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/admin/dashboard');
        $crawler = $client->followRedirect();
        $form = $crawler->selectButton('_submit')->form();
        $crawler = $client->submit($form, array('_username' => 'wrong name', '_password' => 'wrong password'));
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Bad credentials")')->count() == 1);
    }

    public function testLoginSuccess()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/en/admin/dashboard');
        $form = $crawler->selectButton('_submit')->form();
        $crawler = $client->submit($form, array('_username' => 'root', '_password' => 'root'));

        $this->assertTrue($crawler->filter('html:contains("Dashboard")')->count() == 1);

        return $client;
    }

    /**
     * @depends testLoginSuccess
     * @param $client
     */
    public function testLocaleChange($client)
    {
        $crawler = $client->request('GET', '/switch_locale/fr');
        $this->assertTrue($crawler->filter('html:contains("Tableau de bord")')->count() == 1);

        return $client;
    }

    /**
     * @depends testLocaleChange
     * @param $client
     */
    public function testLogoutSuccess($client)
    {
        $crawler = $client->request('GET', '/admin/logout');
        $this->assertTrue($crawler->filter('html:contains("Username:")')->count() == 1);
        $this->assertTrue($crawler->filter('html:contains("Password:")')->count() == 1);
    }
}
