<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportConfigValuesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'config_values';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->config)) {
            foreach ($sx->config->children() as $key) {
                $pkg = static::getPackageObject($key['package']);
                $node = (string) $key->getName();
                if ($node != 'option') {
                    // legacy
                    $option = $node;
                } else {
                    $option = (string) $key['name'];
                }

                $value = (string) $key;
                if ($value === 'false') {
                    $value = false;
                }
                if (is_object($pkg)) {
                    \Config::save($pkg->getPackageHandle() . '::' . $option, $value);
                } else {
                    \Config::save($option, $value);
                }
            }
        }
    }

}
