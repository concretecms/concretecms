<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Page;

class PageItem extends AbstractItem
{
    public function getDisplayName()
    {
        return t('Page');
    }

    public function getContentObject()
    {
        if ($this->getReference() == '/' || $this->getReference() == '') {
            return Page::getByID(Page::getHomePageID(), 'ACTIVE');
        }

        $c = Page::getByPath($this->getReference(), 'ACTIVE');
        if (is_object($c) && !$c->isError()) {
            return $c;
        }
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return sprintf("{CCM:CID_%s}", $o->getCollectionID());
        }
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getCollectionID();
        }
    }
}
