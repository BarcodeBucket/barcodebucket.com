<?php

namespace BarcodeBucket\Data;

use Doctrine\DBAL\Connection;

class BarcodeService
{
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
    public function __construct(Connection $db, UUIDGeneratorInterface $UUIDGenerator)
    {
        $this->db = $db;
        $this->UUIDGenerator = $UUIDGenerator;
    }

    public function upsert($gtin)
    {
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
        }

        return $uuid;
    }
}
