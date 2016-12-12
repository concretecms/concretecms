<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportAttributeSetsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attribute_sets';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributesets)) {
            foreach ($sx->attributesets->attributeset as $as) {
                $set = \Concrete\Core\Attribute\Set::getByHandle((string) $as['handle']);
                $akc = \Concrete\Core\Attribute\Key\Category::getByHandle($as['category']);
                $controller = $akc->getController();
                $manager = $controller->getSetManager();
                if (!is_object($set)) {
                    $pkg = static::getPackageObject($as['package']);
                    $set = $manager->addSet((string) $as['handle'], (string) $as['name'], $pkg, $as['locked']);
                }
                foreach ($as->children() as $ask) {
                    $ak = $controller->getAttributeKeyByHandle((string) $ask['handle']);
                    if (is_object($ak)) {
                        $manager->addKey($set, $ak);
                    }
                }
            }
        }
    }

}
