<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem;

class ImageRoutine extends AbstractRegularExpressionRoutine
{

    public function getHandle()
    {
        return 'image';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:image:(.*?)\}/i';
    }

    public function getItem($identifier)
    {
        return new ImageItem($identifier);
    }


}
