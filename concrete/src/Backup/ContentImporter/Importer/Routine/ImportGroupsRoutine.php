<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\User\Group\Group;
use SimpleXMLElement;

class ImportGroupsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'groups';
    }

    public function import(SimpleXMLElement $sx)
    {
        if (isset($sx->groups)) {
            $groups = [];
            foreach ($sx->groups->group as $g) {
                $name = (string) $g['name'];
                $path = trim((string) $g['path'], '/');
                $groups[] = [
                    'name' => $name,
                    'path' => ($path === '') ? "/$name" : "/$path",
                    'description' => (string) $g['description'],
                    'package' => (string) $g['package'],
                ];
            }

            usort($groups, function ($a, $b) {
                return count(explode('/', $a['path'])) - count(explode('/', $b['path']));
            });

            foreach ($groups as $group) {
                $existingGroup = Group::getByPath($group['path']);
                if ($existingGroup === null) {
                    $pathChunks = explode('/', $group['path']);
                    if (count($pathChunks) === 2) {
                        $parentGroup = null;
                    } else {
                        array_pop($pathChunks);
                        $parentPath = implode('/', $pathChunks);
                        $parentGroup = Group::getByPath($parentPath);
                    }

                    $pkg = static::getPackageObject($group['package']);
                    Group::add($group['name'], $group['description'], $parentGroup, $pkg);
                }
            }
        }
    }
}
