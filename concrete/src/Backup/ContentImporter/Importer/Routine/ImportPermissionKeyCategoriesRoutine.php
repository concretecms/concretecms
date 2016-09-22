<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Permission\Category;

class ImportPermissionKeyCategoriesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'permission_key_categories';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissioncategories)) {
            foreach ($sx->permissioncategories->category as $pkc) {
                $pkg = static::getPackageObject($pkc['package']);
                $category = Category::getByHandle((string) $pkc['handle']);
                if (!is_object($category)) {
                    Category::add((string) $pkc['handle'], $pkg);
                }
            }
        }
    }
}
