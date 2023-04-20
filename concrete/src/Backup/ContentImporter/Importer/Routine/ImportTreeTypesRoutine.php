<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Tree\TreeType;

class ImportTreeTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'tree_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->treetypes)) {
            foreach ($sx->treetypes->treetype as $t) {
                $type = TreeType::getByHandle((string) $t['handle']);
                if (!$type) {
                    $pkg = static::getPackageObject((string)$t['package']);
                    TreeType::add((string)$t['handle'], $pkg);
                }
            }
        }
    }
}
