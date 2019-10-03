<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem;

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
        $prefix = null;
        $filename = null;
        if (strpos($identifier, ':') > -1) {
            list($prefix, $filename) = explode(':', $identifier);
        } else {
            $filename = $identifier;
        }
        return new ImageItem($filename, $prefix);
    }
}
