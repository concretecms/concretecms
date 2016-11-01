<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;

class ImportPermissionAccessEntityTypesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'permission_access_entity_types';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissionaccessentitytypes)) {
            foreach ($sx->permissionaccessentitytypes->permissionaccessentitytype as $pt) {
                $type = Type::getByHandle((string) $pt['handle']);
                if (!is_object($type)) {
                    $pkg = static::getPackageObject($pt['package']);
                    $name = $pt['name'];
                    if (!$name) {
                        $name = \Core::make('helper/text')->unhandle($pt['handle']);
                    }
                    $type = Type::add($pt['handle'], $name, $pkg);
                }

                if (isset($pt->categories)) {
                    foreach ($pt->categories->children() as $cat) {
                        $catobj = Category::getByHandle((string) $cat['handle']);
                        $catobj->associateAccessEntityType($type);
                    }
                }
            }
        }
    }
}
