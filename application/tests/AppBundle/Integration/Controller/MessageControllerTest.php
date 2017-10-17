<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\HttpKernel\Client;
use Symfony\Component\DomCrawler\Crawler;
use Tests\AppBundle\Integration\Controller\ControllerTestCase;

class MessageControllerTest extends ControllerTestCase
{
    /**
     * @var Client $client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects(true);
    }

    /**
     *  @var Client $client
     *  @var Crawler $crawler
     */
    public function testNewAction()
    {
        $this->logIn('admin', 'admin');

        $crawler      = $this->client->request('GET', 'guestbook/new');
        $form         = $crawler->selectButton('submit')
                        ->form(['appbundle_guestbook[message]' => '!admin!'], 'POST');

        $crawler      = $this->client->submit($form);
        $crawler_text = $crawler->text();

        $this->assertContains('!admin!', $crawler_text);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testNewActionWithoutPermission()
    {
        $crawler = $this->client->request('GET', 'guestbook/new');
        $this->assertEquals('http://localhost/login', $crawler->getUri());
    }

    public function testEditAction()
    {
        $this->logIn('admin', 'admin');

        $crawler       = $this->client->request('GET', '/guestbook');
        $crawler       = $crawler->selectLink('bearbeiten');
        $crawler_links = $crawler->links();
        $crawler       = $this->client->click($crawler_links[count($crawler_links)-1]);

        $form          = $crawler->selectButton('edit')
                            ->form(['appbundle_guestbook[message]' => '!?admin?!'], 'POST');

        $crawler       = $this->client->submit($form);
        $crawler_text  = $crawler->text();

        $this->assertContains('!?admin?!', $crawler_text);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteAction()
    {
        $this->logIn('admin', 'admin');

        $crawler       = $this->client->request('GET', '/guestbook');
        $crawler       = $crawler->selectLink('bearbeiten');
        $crawler_links = $crawler->links();
        $crawler       = $this->client->click($crawler_links[count($crawler_links)-1]);
        $form          = $crawler->selectButton('delete')->form();
        $crawler       = $this->client->click($form);
        $crawler_text  = $crawler->text();

        $this->assertNotContains('!?admin?!', $crawler_text);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown()
    {
        $this->client = null;
    }


}
