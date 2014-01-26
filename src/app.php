<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'user' => 'barcodebucket',
        'password' => 'barcodebucket',
        'dbname' => 'barcodebucket',
        'host' => 'localhost'
    ),
));

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Barcodebucket/Bundle/MainBundle/Resources/views/',
));

$app['uuid.generator'] = $app->share(function ($app) {
    return new \BarcodeBucket\Data\RhumsaaUUIDGenerator();
});

$app['barcode.service'] = $app->share(function ($app) {
    return new \BarcodeBucket\Data\BarcodeService($app['dispatcher'], $app['db'], $app['uuid.generator']);
});

$app['barcode.validator'] = $app->share(function ($app) {
    return new \Zend\Validator\Barcode('GTIN14');
});

$app['webinforiv.scraper'] = $app->share(function ($app) {
    return new \WebinforivScraper\Scraper(new Goutte\Client());
});

$app['cache'] = $app->share(function () {
    return \Zend\Cache\StorageFactory::factory(array(
        'adapter' => array(
            'name'    => 'filesystem',
            'options' => array('ttl' => 3600),
        ),
        'plugins' => array(
            'exception_handler' => array('throw_exceptions' => false),
        ),
    ));
});
$app['debug'] = true;
return $app;
