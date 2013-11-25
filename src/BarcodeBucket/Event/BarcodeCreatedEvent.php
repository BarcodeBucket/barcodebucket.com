<?php
namespace BarcodeBucket\Event;

use BarcodeBucket\Model\Barcode;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BarcodeCreatedEvent
 * @package BarcodeBucket\Event
 */
class BarcodeCreatedEvent extends Event
{
    private $barcode;

    /**
     * @param Barcode $barcode
     */
    public function __construct(Barcode $barcode)
    {
        $this->barcode = $barcode;
    }
}
