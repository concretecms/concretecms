<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem;

class PageTypeRoutine extends AbstractRegularExpressionRoutine
{
    public function getHandle()
    {
        return 'page_type';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:pagetype:(.*?)\}/i';
    }

    public function getItem($identifier)
    {
        return new PageTypeItem($identifier);
    }
}
