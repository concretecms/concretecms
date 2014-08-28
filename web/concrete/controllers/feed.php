<?php
namespace Concrete\Controller;

class Feed extends \Concrete\Core\Controller\Controller
{
    public function get($identifier)
    {
        $feed = \Concrete\Core\Page\Feed::getByHandle($identifier);
        if (is_object($feed)) {
            header('Content-Type: text/xml');
            $xml = $feed->getOutput();
            print $xml;
        }
        exit;
    }
}