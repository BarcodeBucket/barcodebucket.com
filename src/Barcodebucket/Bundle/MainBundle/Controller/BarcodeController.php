<?php

namespace Barcodebucket\Bundle\MainBundle\Controller;

use Barcodebucket\Bundle\MainBundle\Service\BarcodeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class BarcodeController
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Barcodebucket\Bundle\MainBundle\Service\BarcodeService
     */
    private $barcodeService;

    /**
     * @var \Zend\Validator\Barcode
     */
    private $barcodeValidator;

    /**
     * @param RouterInterface $router
     * @param BarcodeService  $barcodeService
     * @param Barcode         $barcodeValidator
     */
    public function __construct(RouterInterface $router, BarcodeService $barcodeService, Barcode $barcodeValidator)
    {
        $this->router = $router;
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
        $url = $this->router->generate('barcodebucket_main_uuid', ['uuid' => $uuid]);

        return new RedirectResponse($url);
    }

    private function barcodeResponse($uuid, $gtin)
    {
        $response = new JsonResponse([
            'uuid' => $uuid,
            'gtin' => $gtin
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(3600 * 24 * 30);

        return $response;
    }
}
