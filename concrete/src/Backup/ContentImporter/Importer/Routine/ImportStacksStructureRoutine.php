<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Page\Stack\Folder\FolderService;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Category;

class ImportStacksStructureRoutine extends AbstractPageStructureRoutine implements SpecifiableHomePageRoutineInterface
{
    public function getHandle()
    {
        return 'stacks';
    }

    public function setHomePage($page)
    {
        $this->home = $page;
    }

    public function import(\SimpleXMLElement $sx)
    {
        $folderService = new FolderService(\Core::make('app'), \Database::connection());

        $siteTree = null;
        if (isset($this->home)) {
            $siteTree = $this->home->getSiteTreeObject();
        }

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
                        $parent = $folderService->getByPath($parentPath);
                    }
                }

                switch($type) {
                    case 'folder':
                        $folder = $folderService->getByPath($path);
                        if (!is_object($folder)) {
                            $folderService->add($name, $parent);
                        }
                        break;
                    case 'global_area':
                        $s = Stack::getByName($name, 'RECENT', $siteTree);
                        if (!is_object($s)) {
                            Stack::addGlobalArea($name, $siteTree);
                        }
                        break;
                    default:
                        //stack
                        $s = Stack::getByPath($path, 'RECENT', $siteTree);
                        if (!is_object($s)) {
                            Stack::addStack($name, $parent);
                        }
                }
            }
        }    }
}
