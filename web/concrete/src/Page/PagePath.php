<?php

namespace Concrete\Core\Page;
/**
 * @Entity
 * @Table(name="PagePaths")
 */
class PagePath {

    /**
     * @Column(type="text")
     */
    protected $cPath;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $ppID;


    /**
     * @Column(columnDefinition="integer unsigned")
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $cID;

    /**
     * @Column(type="boolean")
     */
    protected $ppIsCanonical = false;

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

    public function setPagePathIsCanonical($ppIsCanonical)
    {
        $this->ppIsCanonical = $ppIsCanonical;
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