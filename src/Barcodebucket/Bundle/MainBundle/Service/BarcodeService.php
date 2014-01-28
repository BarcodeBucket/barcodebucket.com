<?php

namespace Barcodebucket\Bundle\MainBundle\Service;

use Barcodebucket\Bundle\MainBundle\Event\BarcodeCreatedEvent;
use Barcodebucket\Bundle\MainBundle\UUID\UUIDGeneratorInterface;
use BarcodeBucket\Model\Barcode;
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
     * @param EventDispatcherInterface $dispatcher
     * @param Connection               $db
     * @param UUIDGeneratorInterface   $UUIDGenerator
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

            $this->dispatcher->dispatch('barcode.created', new BarcodeCreatedEvent(new Barcode($uuid, $gtin)));
        }

        $this
            ->db
            ->commit()
        ;

        return $uuid;
    }
}