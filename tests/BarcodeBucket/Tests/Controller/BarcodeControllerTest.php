<?php

namespace BarcodeBucket\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;

class BarcodeControllerTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        return require __DIR__.'/../../../../web/index.php';
    }

    public function testBarcode302()
    {
        $client = $this->createClient();
        $client->request('GET', '/barcode/9780321834577');

        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Should be redirected');
    }
}
