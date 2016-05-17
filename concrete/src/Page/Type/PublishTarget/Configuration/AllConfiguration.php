<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

class AllConfiguration extends Configuration
{
    protected $selectorFormFactor;
    protected $startingPointPageID;

    public function canPublishPageTypeBeneathTarget(Type $pagetype, Page $page)
    {
        return true;
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

    public function export($cxml)
    {
        $target = parent::export($cxml);
        if ($this->getStartingPointPageID()) {
            $c = Page::getByID($this->getStartingPointPageID(), 'ACTIVE');
            if (is_object($c) && !$c->isError()) {
                $target->addAttribute('path', $c->getCollectionPath());
            }
        }
        $target->addAttribute('form-factor', $this->getSelectorFormFactor());
    }

    /**
     * @param mixed $startingPointPage
     */
    public function setStartingPointPageID($startingPointPage)
    {
        $this->startingPointPage = $startingPointPage;
    }
}
