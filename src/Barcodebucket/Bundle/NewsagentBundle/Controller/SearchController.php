<?php

namespace Barcodebucket\Bundle\NewsagentBundle\Controller;
use Barcodebucket\Bundle\NewsagentBundle\Scraping\ScrapingService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SearchController
 * @package Barcodebucket\Bundle\NewsagentBundle\Controller
 */
class SearchController
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ScrapingService
     */
    private $scraper;

    /**
     * @param EngineInterface $templating
     * @param RouterInterface $router
     * @param ScrapingService $scraper
     */
    public function __construct(EngineInterface $templating, RouterInterface $router, ScrapingService $scraper)
    {
        $this->templating = $templating;
        $this->router = $router;
        $this->scraper = $scraper;
    }

    /**
     * @param  Request  $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $barcode = $request->query->get('barcode');

        $issue = null;
        if (!empty($barcode) && preg_match('/^[0-9]{18}$', $barcode) > 0) {
            $issue = $this->scraper->loadIssue($barcode);
        }

        return new Response($this->templating->render('BarcodebucketNewsagentBundle:Search:index.html.twig'), ['issue' => $issue]);
    }
}
