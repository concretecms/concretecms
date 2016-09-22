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
                if (is_object($pkg)) {
                    \Config::save($pkg->getPackageHandle() . '::' . $key->getName(), (string) $key);
                } else {
                    \Config::save($key->getName(), (string) $key);
                }
            }
        }
    }

}
