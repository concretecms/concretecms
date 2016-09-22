<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Permission\Category;

class ImportGroupsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'groups';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->groups)) {

            $groups = array();
            foreach ($sx->groups->group as $g) {
                $groups[] = $g;
            }

            usort($groups, function ($a, $b) {
                $pathA = (string) $a['path'];
                $pathB = (string) $b['path'];
                $numA = count(explode('/', $pathA));
                $numB = count(explode('/', $pathB));
                if ($numA == $numB) {
                    return 0;
                } else {
                    return ($numA < $numB) ? -1 : 1;
                }
            });

            foreach($groups as $group) {
                $existingGroup = \Concrete\Core\User\Group\Group::getByPath((string) $group['path']);
                if (!is_object($existingGroup)) {
                    $parent = null;
                    if ((string) $group['path'] != '') {
                        $lastSlash = strrpos((string) $group['path'], '/');
                        $parentPath = substr((string) $group['path'], 0, $lastSlash);
                        if ($parentPath) {
                            $parent = \Concrete\Core\User\Group\Group::getByPath($parentPath);
                        }
                    }

                    $pkg = static::getPackageObject($g['package']);
                    \Concrete\Core\User\Group\Group::add((string) $group['name'], (string) $group['description'], $parent, $pkg);
                }
            }
        }
    }
}
