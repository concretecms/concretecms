<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Type\PublishTarget\Type\Type;
use Concrete\Core\Permission\Category;

class ImportPageTypePublishTargetTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'page_type_publish_target_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->pagetypepublishtargettypes)) {
            foreach ($sx->pagetypepublishtargettypes->type as $th) {
                $pkg = static::getPackageObject($th['package']);
                $ce = Type::add((string) $th['handle'], (string) $th['name'], $pkg);
            }
        }
    }

}
