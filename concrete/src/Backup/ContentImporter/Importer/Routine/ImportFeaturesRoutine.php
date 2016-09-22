<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Permission\Category;

class ImportFeaturesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'features';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->features)) {
            foreach ($sx->features->feature as $fea) {
                $feHasCustomClass = false;
                if ($fea['has-custom-class']) {
                    $feHasCustomClass = true;
                }
                $pkg = static::getPackageObject($fea['package']);
                $fx = Feature::add((string) $fea['handle'], (string) $fea['score'], $feHasCustomClass, $pkg);
            }
        }

    }
}
