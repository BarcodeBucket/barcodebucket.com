<?php

namespace Barcodebucket\Bundle\MainBundle\Service;

use Barcodebucket\Bundle\MainBundle\Entity\BarcodeRepository;
use Barcodebucket\Bundle\MainBundle\Event\BarcodeCreatedEvent;
use Barcodebucket\Bundle\MainBundle\UUID\UUIDGeneratorInterface;
use BarcodeBucket\Model\Barcode;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BarcodeService
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityManager
     */
    private $objectManager;

    /**
     * @var BarcodeRepository
     */
    private $repository;

    /**
     * @var UUIDGeneratorInterface
     */
    private $UUIDGenerator;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager            $objectManager
     * @param UUIDGeneratorInterface   $UUIDGenerator
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $objectManager,
                                UUIDGeneratorInterface $UUIDGenerator)
    {
        $this->dispatcher    = $dispatcher;
        $this->objectManager = $objectManager;
        $this->repository    = $objectManager->getRepository('Barcodebucket\\Bundle\\MainBundle\\Entity\\Barcode');
        $this->UUIDGenerator = $UUIDGenerator;
    }

    /**
     * @param $uuid
     * @return string|null
     */
    public function getBarcode($uuid)
    {
        $barcode = $this->repository->find($uuid);

        if ($barcode != null) {
            return $barcode->getBarcode();
        }

        return null;
    }

    /**
     * @param $gtin
     * @return string
     */
    public function upsert($gtin)
    {
        $this
            ->objectManager
            ->beginTransaction();
        ;

        $barcode = $this->repository->findOneByBarcode($gtin);

        if (null === $barcode) {
            $uuid = $this->UUIDGenerator->generate();

            $barcode = new Barcode($uuid, $gtin);
            $this->objectManager->persist($barcode);
            $this->objectManager->flush($barcode);

            $this->dispatcher->dispatch('barcode.created', new BarcodeCreatedEvent(new Barcode($uuid, $gtin)));
        }

        $this
            ->objectManager
            ->commit()
        ;

        return $barcode->getUuid();
    }
}
