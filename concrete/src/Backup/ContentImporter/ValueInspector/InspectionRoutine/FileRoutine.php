<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;

class FileRoutine extends AbstractRegularExpressionRoutine
{
    public function getHandle()
    {
        return 'file';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:file:(.*?)\}/i';
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
        return new FileItem($filename, $prefix);
    }
}
