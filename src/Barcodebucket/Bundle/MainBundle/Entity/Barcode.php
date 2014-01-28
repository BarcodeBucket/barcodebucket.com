<?php

namespace Barcodebucket\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Barcode
 * @package Barcodebucket\Bundle\MainBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="barcodes")
 */
class Barcode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @var string
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=14, unique=true)
     * @var string
     */
    private $barcode;

    /**
     * @param string $uuid
     * @param string $barcode
     */
    public function __construct($uuid = null, $barcode = null)
    {
        $this->uuid = $uuid;
        $this->barcode = $barcode;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}
