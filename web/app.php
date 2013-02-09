<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Validator\Barcode;

require_once __DIR__.'/../common/appcommon.php';

$app['controllers']
    ->assert('uuid', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}')
;

$app->get('/barcode/{gtin}', function($gtin) use ($app) {
    $gtin = sprintf('%014d', $gtin);
    $validator = new Barcode('GTIN14');
    if(!$validator->isValid($gtin)) {
        throw new NotFoundHttpException('Invalid barcode');
    }
    
    $sql = 'SELECT uuid FROM barcodes WHERE barcode = ?';
    $uuid = $app['db']->fetchColumn($sql, array($gtin));
    
    if(false === $uuid) {
        $app['db']
            ->executeUpdate('INSERT INTO barcodes (uuid, barcode) VALUES (?,?)', array(
                $uuid = trim(`uuidgen -r`),
                $gtin
            ))
        ;
    }
    
    return $app->redirect('/barcode/'.$uuid);
})->assert('gtin', '[0-9]{8,14}');

$app->get('/barcode/{uuid}', function($uuid) use ($app) {
    $sql = 'SELECT barcode FROM barcodes WHERE uuid = ?';
    $gtin = $app['db']->fetchColumn($sql, array($uuid));
    
    if(false === $gtin) {
        throw new NotFoundHttpException('UUID not found');
    }
    
    return barcode_response($app, $uuid, $gtin);
});

$app['debug'] = in_array($_SERVER['REMOTE_ADDR'], array ('127.0.0.1'));
$app->run();
