<?php
namespace BarcodeBucket\Event;

use Symfony\Component\EventDispatcher\Event;

class BarcodeCreatedEvent extends Event {

    private $uuid;

    private $gtin;

    /**
     * @param string $uuid
     * @param string $gtin
     */
    public function __construct($uuid, $gtin) {
        $this->uuid = $uuid;
        $this->gtin = $gtin;

        $this->setName('barcode.created');
    }

    /**
     * @return string
     */
    public function getGtin()
    {
        return $this->gtin;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}