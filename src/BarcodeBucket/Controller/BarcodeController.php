<?php
namespace BarcodeBucket\Controller;

use BarcodeBucket\Data\BarcodeService;
use Silex\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class BarcodeController
{
    /**
     * @var \Silex\Application
     */
    private $application;

    /**
     * @var \BarcodeBucket\Data\BarcodeService
     */
    private $barcodeService;

    /**
     * @var \Zend\Validator\Barcode
     */
    private $barcodeValidator;

    /**
     * @param BarcodeService $barcodeService
     */
    public function __construct(Application $application, BarcodeService $barcodeService, Barcode $barcodeValidator)
    {
        $this->application = $application;
        $this->barcodeService = $barcodeService;
        $this->barcodeValidator = $barcodeValidator;
    }

    public function uuidAction($uuid)
    {
        $gtin = $this->barcodeService->getBarcode($uuid);

        if (false === $gtin) {
            throw new NotFoundHttpException('UUID not found');
        }

        return $this->barcodeResponse($uuid, $gtin);
    }

    public function gtinAction($gtin)
    {
        $gtin = sprintf('%014d', $gtin);

        if (!$this->barcodeValidator->isValid($gtin)) {
            throw new NotFoundHttpException('Invalid barcode');
        }

        $uuid = $this->barcodeService->upsert($gtin);

        return $this->application->redirect('/barcode/'.$uuid);
    }

    private function barcodeResponse($uuid, $gtin)
    {
        $response = $this
            ->application
            ->json(array(
                'uuid' => $uuid,
                'gtin' => $gtin
            ))
        ;

        $response->setPublic();
        $response->setSharedMaxAge(3600 * 24 * 30);

        return $response;
    }
}
