<?php
/**
 * Created by PhpStorm.
 * User: LaserGehirn
 * Date: 12.10.2017
 * Time: 12:46
 */

namespace Tests\AppBundle\Integration\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class ControllerTestCase extends WebTestCase
{
    /**
     *
     */
    protected function logIn($username, $password)
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('login')
            ->form(
                [
                    'username' => $username,
                    'password' => $password
                ],
                'POST'
            );
        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param Crawler $crawler
     * @param $index
     * @param $constraint
     * @return array
     */
    protected function getTableRecordBy($crawler, $index, $constraint)
    {
        $rows               = [];
        $row                = [];
        $table_rows_crawler = $crawler->filterXPath('//table/tr');
        foreach ($table_rows_crawler as $trc => $content) {
            $crawler = new Crawler($content);
            $table_dates_crawler = $crawler->filterXPath('//td');
            $table_dates = [];
            foreach ($table_dates_crawler as $date => $date_content) {
                $table_dates[] = $date_content->nodeValue;
            }
            if ($table_dates[$index] === $constraint) {
                $row = $table_dates;
            }
            $rows[] = $row;
        }

        return $rows;
    }
}