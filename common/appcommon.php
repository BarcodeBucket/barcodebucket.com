<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../db/app.db',
    ),
));