<?php

namespace Concrete\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Feed as PageFeed;
use Symfony\Component\HttpFoundation\Response;

class Feed extends Controller
{
    public function output($identifier)
    {
        if ($feed = PageFeed::getByHandle($identifier)) {
            if ($xml = $feed->getOutput($this->request)) {
                return Response::create($xml, 200, ['Content-Type' => 'text/xml']);
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->notFound(t('Unable to find the requested RSS feed.'));
    }
}
