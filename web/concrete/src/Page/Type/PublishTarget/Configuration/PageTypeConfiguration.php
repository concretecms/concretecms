<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Loader;
use PageType;

class PageTypeConfiguration extends Configuration
{

    protected $ptID;
    protected $selectorFormFactor;
    protected $startingPointPage;

    public function setPageTypeID($ptID)
    {
        $this->ptID = $ptID;
    }

    public function getPageTypeID()
    {
        return $this->ptID;
    }

    public function getSelectorFormFactor()
    {
        return $this->selectorFormFactor;
    }

    public function setSelectorFormFactor($selectorFormFactor)
    {
        $this->selectorFormFactor = $selectorFormFactor;
    }

    /**
     * @return mixed
     */
    public function getStartingPointPageID()
    {
        return $this->startingPointPage;
    }

    /**
     * @param mixed $startingPointPage
     */
    public function setStartingPointPageID($startingPointPage)
    {
        $this->startingPointPage = $startingPointPage;
    }

    public function export($cxml)
    {
        $target = parent::export($cxml);
        if ($this->getStartingPointPageID()) {
            $c = Page::getByID($this->getStartingPointPageID(), 'ACTIVE');
            if (is_object($c) && !$c->isError()) {
                $target->addAttribute('path', $c->getCollectionPath());
            }
        }
        $ct = PageType::getByID($this->ptID);
        if (is_object($ct)) {
            $target->addAttribute('pagetype', $ct->getPageTypeHandle());
        }
        $target->addAttribute('form-factor', $this->getSelectorFormFactor());
    }

    public function canPublishPageTypeBeneathTarget(Type $pagetype, Page $page)
    {
        return $page->getPageTypeID() == $this->getPageTypeID();
    }

}