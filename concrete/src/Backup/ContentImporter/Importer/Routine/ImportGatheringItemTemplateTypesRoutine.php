<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Gathering\Item\Template\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportGatheringItemTemplateTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'gathering_item_template_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->gatheringitemtemplatetypes)) {
            foreach ($sx->gatheringitemtemplatetypes->gatheringitemtemplatetype as $at) {
                $pkg = static::getPackageObject($at['package']);
                Type::add((string) $at['handle'], $pkg);
            }
        }
    }

}
