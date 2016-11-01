<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Permission\Category;

class ImportBlockTypeSetsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'block_type_sets';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->blocktypesets)) {
            foreach ($sx->blocktypesets->blocktypeset as $bts) {
                $pkg = static::getPackageObject($bts['package']);
                $set = Set::getByHandle((string) $bts['handle']);
                if (!is_object($set)) {
                    $set = Set::add((string) $bts['handle'], (string) $bts['name'], $pkg);
                }
                foreach ($bts->children() as $btk) {
                    $bt = BlockType::getByHandle((string) $btk['handle']);
                    if (is_object($bt)) {
                        $set->addBlockType($bt);
                    }
                }
            }
        }

    }
}
