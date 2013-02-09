<?php

require_once __DIR__.'/../common/appcommon.php';

$app->get('/barcode/{barcode}', function($barcode) use ($app) {
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

    return $app
                ->json(array(
                    'uuid' => $uuid,
                    'barcode' => $barcode
                ))
           ;
})->assert('barcode', '[0-9]{13,18}');

$app['debug'] = true;
$app->run();
