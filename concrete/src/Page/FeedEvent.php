<?php
namespace Concrete\Core\Page;

use \Symfony\Component\EventDispatcher\GenericEvent;
use Concrete\Core\Http\RequestEventInterface;
use Symfony\Component\HttpFoundation\Request;

class FeedEvent extends GenericEvent implements RequestEventInterface
{

    protected $feed;
    protected $writer;
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /** @return \Symfony\Component\HttpFoundation\Request */
    public function getRequest()
    {
        return $this->request;
    }


    public function getPageObject()
    {
        return $this->page;
    }

    public function setPageObject(Page $c)
    {
        $this->page = $c;
    }

    public function getFeedObject()
    {
        return $this->feed;
    }

    public function setFeedObject($feed)
    {
        return $this->feed = $feed;
    }

    public function setWriterObject($writer)
    {
        return $this->writer = $writer;
    }

    public function getWriterObject()
    {
        return $this->writer;
    }


}
