<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class GuestbookControllerTest
 * @package AppBundle\Tests\Controller
 *
 * @codeCoverageIgnore
 */
class GuestbookControllerTest extends WebTestCase
{
    /**
     * @var Client|null
     */
    private $client = null;

    /**
     *
     */
    protected function setUp()
    {
        // Create a new client to browse the application
        $this->client = static::createClient();
    }

    /**
     *
     */
    public function testNewAction()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/guestbook/new');
        var_dump($crawler->getUri());
        //$form = $crawler->selectButton('anlegen')
        $crawler->filter('');
        $form = $crawler->form(['appbundle_guestbook[message]' => 'Test'])
            ->setMethod('POST');
        $this->client->submit($form);
        var_dump($crawler->getUri());

    }


    /**
     *
     */
    public function testCompleteScenario()
    {

        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /login");

        $form = $crawler->selectButton('login')
                ->form(['username' => 'user1', 'password' => 'user1']);
        $this->client->submit($form);
        //var_dump($crawler->getUri());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for submit Login form");
        //var_dump($crawler->getUri());

        $this->logIn();
        $crawler = $this->client->request('GET', '/guestbook');
        //var_dump($crawler->getUri());

        $crawler = $this->client->click($crawler->selectLink('Neue Nachricht schreiben')->link());

        //var_dump($crawler->getUri());
        // Fill in the form and submit it
        $form = $crawler->selectButton('anlegen')
                        ->form(['appbundle_guestbook[message]' => 'Test'])
                        ->setMethod('POST');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('div:contains("Test")')->count(), 'Missing element div:contains("Test")');
        /*
        // Edit the entity
        $crawler = $client->click($crawler->selectLink('bearbeiten')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'appbundle_guestbook[field_name]' => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
        */
    }

    /**
     *
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        $token = new UsernamePasswordToken('user1', null, 'main', ['ROLE_USER']);
        $session->set('_security_main', serialize($token));
        //$this->getContainer()->get('security.token_storage')->setToken($token);
    }
}
