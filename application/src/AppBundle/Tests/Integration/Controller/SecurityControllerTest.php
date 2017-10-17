<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest
 * @package AppBundle\Tests\Controller
 *
 * @codeCoverageIgnore
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     *
     */
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
    }

}
