<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Validation\BannedWord\BannedWord;

class ImportPermissionsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'permissions';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->permissionkeys)) {
            foreach ($sx->permissionkeys->permissionkey as $pk) {
                if (is_object(Key::getByHandle((string) $pk['handle']))) {
                    continue;
                }
                $pkc = Category::getByHandle((string) $pk['category']);
                $c1 = $pkc->getPermissionKeyClass();
                $pkx = call_user_func(array($c1, 'import'), $pk);
                $assignments = array();

                if (isset($pk->access)) {
                    foreach ($pk->access->children() as $ch) {
                        if ($ch->getName() == 'group') {
                            /*
                             * Legacy
                             */
                            $g = Group::getByName($ch['name']);
                            if (!is_object($g)) {
                                $g = Group::add($ch['name'], $ch['description']);
                            }
                            $pae = GroupEntity::getOrCreate($g);
                            $assignments[] = $pae;
                        }

                        if ($ch->getName() == 'entity') {
                            $type = Type::getByHandle((string) $ch['type']);
                            $class = $type->getAccessEntityTypeClass();
                            if (method_exists($class, 'configureFromImport')) {
                                $pae = $class::configureFromImport($ch);
                                $assignments[] = $pae;
                            }
                        }
                    }
                }

                if (count($assignments)) {
                    $pa = Access::create($pkx);
                    foreach ($assignments as $pae) {
                        $pa->addListItem($pae);
                    }
                    $pt = $pkx->getPermissionAssignmentObject();
                    $pt->assignPermissionAccess($pa);
                }
            }
        }
    }

}
