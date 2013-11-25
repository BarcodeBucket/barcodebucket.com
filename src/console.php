<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('BarcodeBucket', '0.1.x-dev');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console
    ->register('database:init')
    ->setDescription('Database initialization')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $app['db']->executeUpdate(<<<EOT
CREATE TABLE barcodes (
    uuid CHAR(36) PRIMARY KEY,
    barcode CHAR(14) UNIQUE
)
EOT
        );
    });
$console
    ->register('database:empty')
    ->setDescription('Deletes all data from database')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $app['db']->executeUpdate('DELETE FROM barcodes');
    })
;

return $console;
