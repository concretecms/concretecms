<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Category;

class ImportStacksStructureRoutine extends AbstractPageStructureRoutine
{
    public function getHandle()
    {
        return 'stacks';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->stacks)) {

            $nodes = array();
            $i = 0;
            foreach ($sx->stacks->children() as $p) {
                $p->originalPos = $i;
                $nodes[] = $p;
                ++$i;
            }
            usort($nodes, array('static', 'setupPageNodeOrder'));

            foreach ($nodes as $p) {

                $parent = null;
                $path = (string) $p['path'];
                if ($p->getName() == 'folder') {
                    $type = 'folder';
                } else {
                    $type = (string) $p['type'];
                }
                $name = (string) $p['name'];
                if ($path) {
                    $lastSlash = strrpos($path, '/');
                    $parentPath = substr($path, 0, $lastSlash);
                    if ($parentPath) {
                        $parent = StackFolder::getByPath($parentPath);
                    }
                }

                switch($type) {
                    case 'folder':
                        $folder = Folder::getByPath($path);
                        if (!is_object($folder)) {
                            Folder::add($name, $parent);
                        }
                        break;
                    case 'global_area':
                        $s = Stack::getByName($name);
                        if (!is_object($s)) {
                            Stack::addGlobalArea($name);
                        }
                        break;
                    default:
                        //stack
                        $s = Stack::getByPath($path);
                        if (!is_object($s)) {
                            Stack::addStack($name, $parent);
                        }
                }
            }
        }    }
}
