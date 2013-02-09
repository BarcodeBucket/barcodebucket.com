<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Validator\Barcode;

require_once __DIR__.'/../common/appcommon.php';

$app->get('/barcode/{barcode}', function($barcode) use ($app) {
    $validator = new Barcode('Ean13');
    if(!$validator->isValid($barcode)) {
        throw new NotFoundHttpException('Invalid EAN');
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
            'barcode' => $barcode
        ))
    ;
    
    $response->setPublic();
    $response->setSharedMaxAge(3600 * 24 * 30);
    
    return $response;
})->assert('barcode', '[0-9]{13,18}');

$app->get('/barcode', function() use ($app) {
    $data = $app['db']->fetchAll('SELECT * FROM barcodes');
    return $app->json($data);
});

$app['debug'] = in_array($_SERVER['REMOTE_ADDR'], array ('127.0.0.1'));
$app->run();
