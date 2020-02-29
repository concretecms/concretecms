<?php
namespace Concrete\Controller\Frontend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Site\Selector;
use Concrete\Core\Site\Service;
use Symfony\Component\HttpFoundation\JsonResponse;

class SelectSite extends AbstractController
{

    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Selector
     */
    protected $selector;

    public function __construct(Selector $selector, Service $service)
    {
        $this->selector = $selector;
        $this->service = $service;
        parent::__construct();
    }

    public function select($siteID)
    {
        $uri = $this->request->query->get('rUri');
        $token = $this->app->make('token');
        if ($uri && $token->validate($uri)) {
            $site = $this->service->getByID($siteID);
            if (is_object($site)) {
                $this->selector->saveSiteToSession($site);
                return new JsonResponse([]);
            }
        }

        $this->app->shutdown();
    }

    public function selectAndRedirect($siteID)
    {
        $uri = $this->request->query->get('rUri');
        $token = $this->app->make('token');
        if ($uri && $token->validate($uri)) {
            $site = $this->service->getByID($siteID);
            if (is_object($site)) {
                $this->selector->saveSiteToSession($site);
                return new RedirectResponse($uri);
            }
        }

        $this->app->shutdown();
    }



}
