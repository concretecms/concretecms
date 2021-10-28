<?php
namespace Concrete\Core\Page;


class PagePathEvent
{
    protected $page;
    protected $path;

    public function __construct(Page $c)
    {
        $this->page = $c;
    }

    public function setPagePath($path)
    {
        $this->path = $path;
    }

    public function getPageObject()
    {
        return $this->page;
    }

    public function getPagePath()
    {
        return $this->path;
    }
}
