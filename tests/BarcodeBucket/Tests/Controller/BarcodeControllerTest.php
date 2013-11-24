<?php

namespace BarcodeBucket\Tests\Controller;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernel;

class BarcodeControllerTest extends WebTestCase
{
    const UUID_REGEXP = '/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/';

    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../src/app.php';
        require __DIR__.'/../../../../config/test.php';
        require __DIR__.'/../../../../src/controllers.php';

        return $app;
    }

    public function testBarcode302()
    {
        $client = $this->createClient();
        $client->request('GET', '/barcode/9780321834577');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect(), 'Should be redirected');
        $this->assertRegExp(self::UUID_REGEXP, $response->headers->get('Location'));

        $client->followRedirect();

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Should be found');

        $json = json_decode($response->getContent());
        $this->assertEquals('09780321834577',$json->gtin);
        $this->assertRegExp(self::UUID_REGEXP, $json->uuid);
    }
}
