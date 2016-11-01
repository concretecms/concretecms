<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Type\Composer\Control\Type\Type;
use Concrete\Core\Permission\Category;

class ImportPageTypeComposerControlTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_type_composer_control_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypecomposercontroltypes)) {
            foreach ($sx->pagetypecomposercontroltypes->type as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = Type::add((string) $th['handle'], (string) $th['name'], $pkg);
            }
        }
    }

}
