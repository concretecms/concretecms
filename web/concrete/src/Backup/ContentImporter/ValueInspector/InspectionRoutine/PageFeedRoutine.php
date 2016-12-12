<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem;

class PageFeedRoutine extends AbstractRegularExpressionRoutine
{

    public function getHandle()
    {
        return 'page_feed';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:pagefeed:(.*?)\}/i';
    }

    public function getItem($identifier)
    {
        return new PageFeedItem($identifier);
    }


}
