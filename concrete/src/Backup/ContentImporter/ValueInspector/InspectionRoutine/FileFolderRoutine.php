<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileFolderItem;

class FileFolderRoutine extends AbstractRegularExpressionRoutine
{
    public function getHandle()
    {
        return 'file_folder';
    }

    public function getRegularExpression()
    {
        return '/{ccm:export:filefolder:(.*?)\}/i';
    }

    public function getItem($identifier)
    {
        return new FileFolderItem($identifier);
    }
}
