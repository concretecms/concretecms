<?php

namespace Concrete\TestHelpers\Backup;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem;

class CustomPageRoutine extends PageRoutine
{
    public function getItem($identifier)
    {
        $identifier = '/page-2' . $identifier;

        return new PageItem($identifier);
    }
}
