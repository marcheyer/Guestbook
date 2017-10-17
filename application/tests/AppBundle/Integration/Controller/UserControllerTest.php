<?php

namespace Tests\AppBundle\Controller;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Client;
use Tests\AppBundle\Integration\Controller\ControllerTestCase;

/**
 * Class UserControllerTest
 * @package Tests\AppBundle\Controller
 */
class UserControllerTest extends ControllerTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     *
     */
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects(true);
    }

    /**
     *
     */
    public function testIndexActionAsAdmin()
    {
        $this->logIn('admin', 'admin');
        $crawler = $this->client->request('GET', '/user/index');
        $crawler = $crawler->filterXPath('//div[@class="row"]')->slice(3);
        foreach ($crawler as $row) {
            $this->assertContains('bearbeiten', $row->textContent);
        }

        $this->client->request('GET', '/guestbook/logout');
    }

    public function testIndexActionWithoutPermission()
    {
        $crawler = $this->client->request('GET', '/user/index');
        $this->assertEquals('http://localhost/', $crawler->getUri());
    }

    /**
     *
     */
    public function testNewAction()
    {
        $crawler = $this->client->request('GET', '/register');
        $form    = $crawler->selectButton('registerBtn')
                        ->form(
                            [
                                'appbundle_user[username]' => 'xyz',
                                'appbundle_user[password][first]' => 'y',
                                'appbundle_user[password][second]' => 'y',
                                'appbundle_user[email]' => 'xyz@x'
                            ],
                            'POST');
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/user/index');

        $this->assertContains('xyz@x', $crawler->html());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     *
     */
    public function testEditAction()
    {
        $this->logIn('xyz', 'y');

        $crawler       = $this->client->request('GET', '/user/index');
        $crawler       = $crawler->selectLink('bearbeiten');
        $crawler_links = $crawler->links();
        $crawler       = $this->client->click($crawler_links[0]);

        $form = $crawler->selectButton('edit')
            ->form(
                [
                    'appbundle_user[password][first]' => 'x',
                    'appbundle_user[password][second]' => 'x',
                    'appbundle_user[email]' => 'xyz@x'
                ],
                'POST');

        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/guestbook/logout');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     *
     */
    public function testDeleteAction()
    {
        $this->logIn('xyz', 'x');

        $crawler        = $this->client->request('GET', '/user/index');
        $crawler        = $crawler->selectLink('bearbeiten');
        $crawler_links  = $crawler->links();

        $crawler = $this->client->click($crawler_links[0]);

        $form           = $crawler->selectButton('Benutzer löschen')
                                    ->form();

        $crawler        = $this->client->submit($form);
        $crawler_text   = $crawler->html();

        $this->assertNotContains('xyz', $crawler_text);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Deletes the client reference
     */
    protected function tearDown()
    {
        $this->client = null;
    }
}
