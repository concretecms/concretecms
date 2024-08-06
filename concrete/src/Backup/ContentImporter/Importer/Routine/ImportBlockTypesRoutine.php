<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;

class ImportBlockTypesRoutine extends AbstractRoutine implements SpecifiableImportModeRoutineInterface
{

    protected $importMode = null;

    public function getHandle()
    {
        return 'block_types';
    }

    public function setImportMode(string $importMode): void
    {
        $this->importMode = $importMode;
    }

    public function import(\SimpleXMLElement $sx)
    {
        $importMode = $this->importMode;
        if (!$importMode) {
            $importMode = ContentImporter::IMPORT_MODE_UPGRADE; // fallback for backward compatibility
        }
        if (isset($sx->blocktypes)) {
            foreach ($sx->blocktypes->blocktype as $bt) {
                if (!is_object(BlockType::getByHandle((string) $bt['handle']))) {
                    $pkg = static::getPackageObject($bt['package']);
                    if (is_object($pkg)) {
                        BlockType::installBlockTypeFromPackage((string) $bt['handle'], $pkg, $importMode);
                    } else {
                        BlockType::installBlockType((string) $bt['handle'], null, $importMode);
                    }
                }
            }
        }

    }

}
