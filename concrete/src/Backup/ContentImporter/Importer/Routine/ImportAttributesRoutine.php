<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportAttributesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attributes';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributekeys)) {
            foreach ($sx->attributekeys->attributekey as $ak) {
                $akc = \Concrete\Core\Attribute\Key\Category::getByHandle($ak['category']);
                $controller = $akc->getController();
                $attribute = $controller->getAttributeKeyByHandle((string) $ak['handle']);
                if (!$attribute) {
                    if (!method_exists($controller, 'import')) {
                        throw new \RuntimeException(t('The attribute category %s does not support importing the attribute keys.', (string) $ak['category']));
                    }
                    $pkg = static::getPackageObject($ak['package']);
                    $type = Type::getByHandle((string) $ak['type']);
                    $key = $controller->import($type, $ak, $pkg);
                }
            }
        }
    }

}
