<?php
namespace Barcodebucket\Bundle\NewsagentBundle\Controller;

use Barcodebucket\Bundle\MainBundle\Service\BarcodeService;
use Barcodebucket\Bundle\NewsagentBundle\Scraping\ScrapingService;
use Barcodebucket\Scraper\Model\Issue;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Zend\Validator\Barcode;

/**
 * Class BarcodeController
 * @package BarcodeBucket\Controller
 */
class WebinforivController
{
    /**
     * @var ScrapingService
     */
    private $scraper;

    /**
     * @var BarcodeService
     */
    private $barcodeService;

    /**
     * @var \Zend\Validator\Barcode
     */
    private $barcodeValidator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ScrapingService $scraper
     * @param BarcodeService  $barcodeService
     * @param Barcode         $barcodeValidator
     * @param RouterInterface $router
     */
    public function __construct(ScrapingService $scraper, BarcodeService $barcodeService,
                                Barcode $barcodeValidator, RouterInterface $router)
    {
        $this->scraper = $scraper;
        $this->barcodeService = $barcodeService;
        $this->barcodeValidator = $barcodeValidator;
        $this->router = $router;
    }

    /**
     * @param  Request      $request
     * @param  string       $fullBarcode
     * @return JsonResponse
     */
    public function barcodeAction(Request $request, $fullBarcode)
    {
        $gtin = $this->getGtin($fullBarcode);
        $issue = $this->loadIssueOrThrowNotFoundException($fullBarcode, $gtin);

        $uuid = $this->barcodeService->upsert($gtin);

        $response = new JsonResponse($this->issueToArray($uuid, $issue));
        $response->setPublic();
        $response->setLastModified($issue->getLastUpdate());
        $response->isNotModified($request);

        return $response;
    }

    /**
     * @param  Request                                                       $request
     * @param $fullBarcode
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function pictureAction(Request $request, $fullBarcode)
    {
        $gtin = $this->getGtin($fullBarcode);
        $issue = $this->loadIssueOrThrowNotFoundException($fullBarcode, $gtin);
        $binaryPicture = $this->scraper->loadPicture($issue);

        if ($binaryPicture === null) {
            throw new NotFoundHttpException('Picture not found');
        }

        $response = new Response($binaryPicture, 200, ['content-type' => 'image/jpeg']);
        $response->setEtag(sha1($response->getContent()));
        $response->setPublic();
        $response->isNotModified($request);

        return $response;
    }

    /**
     * @param  Issue       $issue
     * @return null|string
     */
    private function getPictureForIssue(Issue $issue)
    {
        if (strlen($issue->getPicture()) === 0) {
            return null;
        }

        return $this->router->generate('barcodebucket_newsagent_picture', ['fullBarcode' => $issue->getBarcode()],
            UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param  string $uuid
     * @param  Issue  $issue
     * @return array
     */
    private function issueToArray($uuid, Issue $issue)
    {
        $fullBarcode = $issue->getBarcode();

        return [
            'barcode' => [
                'uuid' => $uuid,
                'ean' => $this->getBarcode($fullBarcode),
                'gtin' => $this->getGtin($fullBarcode),
            ],
            'addon' => $this->getAddon($fullBarcode),
            'title' => $issue->getTitle(),
            'subtitle' => $issue->getSubtitle(),
            'issueNumber' => $issue->getIssueNumber(),
            'date' => $issue->getDate()->format('Y-m-d'),
            'price' => $issue->getPrice(),
            'picture' => $this->getPictureForIssue($issue),
            'termsOfSale' => [
                'taxRate' => $issue->getTaxRate(),
                'discount' => $issue->getDiscount(),
                'discountCode' => $issue->getDiscountCode(),
                'foldingFee' => $issue->getFoldingFee(),
                'waybillPrice' => $issue->getWaybillPrice(),
                'consigment' => $issue->isConsignment(),
            ],
            'sender' => [
                'id' => $issue->getSenderId(),
                'name' => $issue->getSender()
            ],
            'lastUpdated' => $issue->getLastUpdate()->format(\DateTime::W3C)
        ];
    }

    /**
     * @param  string $fullBarcode
     * @return string
     */
    private function getGtin($fullBarcode)
    {
        return '0' . $this->getBarcode($fullBarcode);
    }

    /**
     * @param  string $fullBarcode
     * @return string
     */
    private function getBarcode($fullBarcode)
    {
        return substr($fullBarcode, 0, 13);
    }

    /**
     * @param $fullBarcode
     * @return string
     */
    private function getAddon($fullBarcode)
    {
        return substr($fullBarcode, 13);
    }

    /**
     * @param $fullBarcode
     * @param $gtin
     * @return null|Issue
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function loadIssueOrThrowNotFoundException($fullBarcode, $gtin)
    {
        if (!$this->barcodeValidator->isValid($gtin) || ($issue = $this->scraper->loadIssue($fullBarcode)) === null) {
            throw new NotFoundHttpException('Issue not found');
        }

        return $issue;
    }
}
