<?php

$app = require_once __DIR__.'/../common/appcommon.php';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['controllers']
    ->assert('uuid', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}')
;

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});

$app->get('/barcode/{gtin}', 'barcode.controller:gtinAction')->assert('gtin', '[0-9]{8,14}');
$app->get('/barcode/{uuid}', 'barcode.controller:uuidAction');

//$app['debug'] = in_array($_SERVER['REMOTE_ADDR'], array ('127.0.0.1'));
$app->run();

return $app;
