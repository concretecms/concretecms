<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportAttributeTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attribute_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributetypes)) {
            foreach ($sx->attributetypes->attributetype as $at) {
                $pkg = static::getPackageObject($at['package']);
                $name = (string) $at['name'];
                if (!$name) {
                    $name = \Core::make('helper/text')->unhandle($at['handle']);
                }
                $type = Type::getByHandle($at['handle']);
                if (!is_object($type)) {
                    $type = Type::add((string) $at['handle'], $name, $pkg);
                }
                if (isset($at->categories)) {
                    foreach ($at->categories->children() as $cat) {
                        $catobj = \Concrete\Core\Attribute\Key\Category::getByHandle((string) $cat['handle']);
                        $catobj->getController()->associateAttributeKeyType($type);
                    }
                }
            }
        }
    }

}
