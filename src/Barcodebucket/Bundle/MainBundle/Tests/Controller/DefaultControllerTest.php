<?php

namespace Barcodebucket\Bundle\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/');

        $this->assertTrue($crawler->filter('html:contains("Validate and store your barcodes")')->count() > 0);
    }
}
