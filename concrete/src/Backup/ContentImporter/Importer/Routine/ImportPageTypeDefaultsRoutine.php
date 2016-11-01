<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Category;

class ImportPageTypeDefaultsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_type_defaults';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypes)) {
            foreach ($sx->pagetypes->pagetype as $p) {
                Type::importContent($p);
            }
        }
    }
}
