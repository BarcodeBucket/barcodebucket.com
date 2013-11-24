<?php

use Silex\Application;

function barcode_response(Application $app, $uuid, $gtin)
{
    $response = $app
        ->json(array(
            'uuid' => $uuid,
            'gtin' => $gtin
        ))
    ;

    $response->setPublic();
    $response->setSharedMaxAge(3600 * 24 * 30);

    return $response;
}
