<?php
namespace BarcodeBucket\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BarcodeCreatedEvent
 * @package BarcodeBucket\Event
 */
class BarcodeCreatedEvent extends Event
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $gtin;

    /**
     * @param string $uuid
     * @param string $gtin
     */
    public function __construct($uuid, $gtin)
    {
        $this->uuid = $uuid;
        $this->gtin = $gtin;
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
