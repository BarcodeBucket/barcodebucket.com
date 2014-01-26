<?php

$app['barcode.controller'] = $app->share(function ($app) {
    return new \BarcodeBucket\Controller\BarcodeController($app, $app['barcode.service'], $app['barcode.validator']);
});

$app['webinforiv.controller'] = $app->share(function ($app) {
    return new \BarcodeBucket\Controller\WebinforivController($app, $app['webinforiv.scraper'], $app['barcode.service'], $app['barcode.validator'], $app['cache']);
});

$app['controllers']
    ->assert('uuid', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}')
;

$app->get('/', function () use ($app) {
    return $app['twig']->render('Default/index.html.twig');
});

$app->get('/barcode/{gtin}', 'barcode.controller:gtinAction')->assert('gtin', '[0-9]{8,14}');
$app->get('/barcode/{uuid}', 'barcode.controller:uuidAction');

$app->get('/scraper/{fullBarcode}', 'webinforiv.controller:barcodeAction')->assert('fullBarcode', '[0-9]{18}');
