<?php

namespace BarcodeBucket\Data;

use BarcodeBucket\Event\BarcodeCreatedEvent;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BarcodeService
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * @var UUIDGeneratorInterface
     */
    private $UUIDGenerator;

    /**
     * Constructor
     *
     * @param Connection             $db
     * @param UUIDGeneratorInterface $UUIDGenerator
     */
    public function __construct(EventDispatcherInterface $dispatcher, Connection $db, UUIDGeneratorInterface $UUIDGenerator)
    {
        $this->dispatcher = $dispatcher;
        $this->db = $db;
        $this->UUIDGenerator = $UUIDGenerator;
    }

    public function getBarcode($uuid)
    {
        $sql = 'SELECT barcode FROM barcodes WHERE uuid = ?';

        return $this->db->fetchColumn($sql, array($uuid));
    }

    public function upsert($gtin)
    {
        $this
            ->db
            ->beginTransaction();
        ;

        $sql = 'SELECT uuid FROM barcodes WHERE barcode = ?';
        $uuid = $this->db->fetchColumn($sql, array($gtin));

        if (false === $uuid) {
            $this
                ->db
                ->executeUpdate('INSERT INTO barcodes (uuid, barcode) VALUES (?,?)', array(
                    $uuid = $this->UUIDGenerator->generate(),
                    $gtin
                ))
            ;

            $this->dispatcher->dispatch(new BarcodeCreatedEvent($uuid, $gtin));
        }

        $this
            ->db
            ->commit()
        ;

        return $uuid;
    }
}
