<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../db/app.sqlite',
    ),
));

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
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

return $app;
