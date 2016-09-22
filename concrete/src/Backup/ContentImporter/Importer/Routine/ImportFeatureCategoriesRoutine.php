<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

class ImportFeatureCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'feature_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->featurecategories)) {
            foreach ($sx->featurecategories->featurecategory as $fea) {
                $pkg = static::getPackageObject($fea['package']);
                $fx = \Concrete\Core\Feature\Category\Category::add($fea['handle'], $pkg);
            }
        }
    }

}
