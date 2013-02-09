<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Validator\Barcode;

require_once __DIR__.'/../common/appcommon.php';

$app->get('/barcode/{barcode}', function($barcode) use ($app) {
    $barcode = sprintf('%014d', $barcode);
    
    return $app->redirect('/barcode/'.$barcode);
})->assert('barcode', '[0-9]{8,13}');

$app->get('/barcode/{barcode}', function($barcode) use ($app) {
    $validator = new Barcode('GTIN14');
    if(!$validator->isValid($barcode)) {
        throw new NotFoundHttpException('Invalid barcode');
    }
    
    $sql = 'SELECT uuid FROM barcodes WHERE barcode = ?';
    $uuid = $app['db']->fetchColumn($sql, array($barcode));
    
    if(false === $uuid) {
        $app['db']
            ->executeUpdate('INSERT INTO barcodes (uuid, barcode) VALUES (?,?)', array(
                $uuid = trim(`uuidgen -r`),
                $barcode
            ))
        ;
    }

    $response = $app
        ->json(array(
            'uuid' => $uuid,
            'gtin' => $barcode
        ))
    ;
    
    $response->setPublic();
    $response->setSharedMaxAge(3600 * 24 * 30);
    
    return $response;
})->assert('barcode', '[0-9]{14}');

$app['debug'] = in_array($_SERVER['REMOTE_ADDR'], array ('127.0.0.1'));
$app->run();
