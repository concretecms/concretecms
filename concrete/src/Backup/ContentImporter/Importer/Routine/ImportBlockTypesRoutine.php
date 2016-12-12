<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;

class ImportBlockTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'block_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->blocktypes)) {
            foreach ($sx->blocktypes->blocktype as $bt) {
                if (!is_object(BlockType::getByHandle((string) $bt['handle']))) {
                    $pkg = static::getPackageObject($bt['package']);
                    if (is_object($pkg)) {
                        BlockType::installBlockTypeFromPackage((string) $bt['handle'], $pkg);
                    } else {
                        BlockType::installBlockType((string) $bt['handle']);
                    }
                }
            }
        }

    }

}
