<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Page\Feed;

class PageFeedItem extends AbstractItem
{

    public function getDisplayName()
    {
        return t('RSS Feed');
    }

    public function getContentObject()
    {
        return Feed::getByHandle($this->getReference());
    }

    public function getContentValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getFeedURL();
        }
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getID();
        }
    }


}