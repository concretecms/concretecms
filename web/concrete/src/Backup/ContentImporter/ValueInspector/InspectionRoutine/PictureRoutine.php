<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem;

class PictureRoutine extends AbstractRegularExpressionRoutine
{
    public function getHandle()
    {
        return 'picture';
    }

    public function getRegularExpression()
    {
        return '/\<concrete-picture\s[^>]*?file\s*=\s*[\'"]([^\'"]*?)[\'"][^>]*?>/i';
    }

    public function getItem($identifier)
    {
        return new PictureItem($identifier);
    }
}
