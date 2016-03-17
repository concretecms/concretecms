<?php
namespace Concrete\Controller;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Page\Feed as PageFeed;
use Symfony\Component\HttpFoundation\Response;

class Feed extends Controller
{

    public function output($identifier)
    {
        if ($feed = PageFeed::getByHandle($identifier)) {

            if ($xml = $feed->getOutput($this->request)) {
                return Response::create($xml, 200, array('Content-Type' => 'text/xml'));
            }
        }

        return Response::create('', 404);
    }

}
