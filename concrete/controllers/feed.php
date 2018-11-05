<?php

namespace Concrete\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Feed as PageFeed;

class Feed extends Controller
{
    public function output($identifier)
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        if ($feed = PageFeed::getByHandle($identifier)) {
            if ($xml = $feed->getOutput($this->request)) {
                return $responseFactory->create($xml, 200, ['Content-Type' => 'text/xml']);
            }
        }

        return $responseFactory->notFound(t('Unable to find the requested RSS feed.'));
    }
}
