<?php

namespace Concrete\Core\Page;

class PagePath {

    protected $cPath;
    protected $ppID;
    protected $cID;
    protected $ppIsCanonical = 0;

    public function getPagePathID()
    {
        return $this->ppID;
    }

    public function getPagePath() {
        return $this->cPath;
    }

    public function setPagePath($path)
    {
        $this->cPath = $path;
    }

    public function setPageObject(Page $c)
    {
        $this->cID = $c->getCollectionID();
    }

    public function isPagePathCanonical()
    {
        return $this->ppIsCanonical;
    }

    public function getCollectionID()
    {
        return $this->cID;
    }

}