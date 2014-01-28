<?php

namespace Barcodebucket\Bundle\MainBundle\Entity;

use BarcodeBucket\Model\Barcode as BaseBarcode;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Barcode
 * @package Barcodebucket\Bundle\MainBundle\Entity
 * @ORM\Entity(repositoryClass="Barcodebucket\Bundle\MainBundle\Entity\BarcodeRepository")
 * @ORM\Table(name="barcodes")
 */
class Barcode extends BaseBarcode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     * @var string
     */
    protected $uuid;

    /**
     * @ORM\Column(name="barcode", type="string", length=14, unique=true)
     * @var string
     */
    protected $gtin;

    /**
     * @param string $uuid
     * @param string $gtin
     */
    public function __construct($uuid = null, $gtin = null)
    {
        parent::__construct($uuid, $gtin);
    }
}
