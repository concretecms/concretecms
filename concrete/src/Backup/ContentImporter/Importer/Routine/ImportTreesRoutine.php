<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Permission\Category;
use Concrete\Core\Tree\Tree;

class ImportTreesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'trees';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->trees)) {
            foreach ($sx->trees->tree as $t) {
                Tree::import($t);
            }
        }
    }
}
