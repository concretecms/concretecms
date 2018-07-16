<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\UserInterface\Sitemap\FlatSitemapProvider;
use Concrete\Core\Application\UserInterface\Sitemap\JsonFormatter;
use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;
use Concrete\Core\Http\ResponseFactoryInterface;

class SitemapData extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::getViewObject()
     */
    public function getViewObject()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        return $this->app->make('helper/concrete/dashboard/sitemap')->canRead();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view()
    {
        $displaySingleLevel = $this->request->request->get('displaySingleLevel');
        if ($displaySingleLevel === null) {
            $displaySingleLevel = $this->request->query->get('displaySingleLevel');
        }
        if ($displaySingleLevel) {
            $provider = $this->app->make(FlatSitemapProvider::class);
        } else {
            $provider = $this->app->make(StandardSitemapProvider::class);
        }
        $formatter = new JsonFormatter($provider);

        return $this->app->make(ResponseFactoryInterface::class)->json($formatter);
    }
}
