<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem;

/**
 * @since 5.7.5.4
 */
class PageRoutine extends AbstractRegularExpressionRoutine
{
    public function getHandle()
    {
        return 'page';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:page:(.*?)\}/i';
    }

    public function getItem($identifier)
    {
        return new PageItem($identifier);
    }
}
