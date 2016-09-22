<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

class ImportAttributeCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'attribute_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->attributecategories)) {
            foreach ($sx->attributecategories->category as $akc) {
                $pkg = static::getPackageObject($akc['package']);
                $akx = \Concrete\Core\Attribute\Key\Category::getByHandle($akc['handle']);
                if (!is_object($akx)) {
                    $akx = \Concrete\Core\Attribute\Key\Category::add((string) $akc['handle'], (string) $akc['allow-sets'], $pkg);
                }
            }
        }
    }

}
