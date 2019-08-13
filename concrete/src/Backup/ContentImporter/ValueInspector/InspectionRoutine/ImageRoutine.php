<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem;

/**
 * @since 5.7.5.4
 */
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
