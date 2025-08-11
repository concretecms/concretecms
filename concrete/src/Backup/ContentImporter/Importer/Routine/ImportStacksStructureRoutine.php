<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use SimpleXMLElement;

class ImportStacksStructureRoutine extends AbstractPageStructureRoutine implements SpecifiableHomePageRoutineInterface
{
    use StackTrait;

    /**
     * @var \Concrete\Core\Page\Page|null
     */
    protected $home;

    /**
     * @var \Concrete\Core\Entity\Site\Tree|null
     */
    private $site;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'stacks';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\SpecifiableHomePageRoutineInterface::setHomePage()
     */
    public function setHomePage($page)
    {
        $this->home = $page;
    }

    public function import(SimpleXMLElement $sx)
    {
        if (!isset($sx->stacks)) {
            return;
        }
        if (!$this->home || $this->home->isError()) {
            $this->home = Page::getByID(Page::getHomePageID(), 'RECENT');
        }
        if (!$this->home || $this->home->isError()) {
            $this->site = null;
        } else {
            $this->site = $this->home->getSite();
        }
        $nodes = [];
        foreach ($sx->stacks->children() as $child) {
            $nodes[] = $child;
        }
        $nodes = $this->sortElementsByPath($nodes, static function (SimpleXMLElement $a, SimpleXMLElement $b) {
            $cmpA = $a->getName() === 'folder' ? 0 : 1;
            $cmpB = $b->getName() === 'folder' ? 0 : 1;

            return $cmpA - $cmpB;
        });
        foreach ($nodes as $p) {
            $this->importElement($p);
        }
    }

    private function importElement(SimpleXMLElement $p)
    {
        $name = (string) $p['name'];
        $path = '/' . trim((string) $p['path'], '/');
        if ($p->getName() == 'folder') {
            $type = 'folder';
        } else {
            $type = (string) $p['type'];
        }
        switch ($type) {
            case 'global_area':
                $globalArea = Stack::getByName($name, 'RECENT', $this->site);
                if (!$globalArea) {
                    Stack::addGlobalArea($name);
                }
                break;
            case 'folder':
                $folderPath = rtrim($path, '/') . '/' . $name;
                if (!array_key_exists($folderPath, $this->getExistingFolders())) {
                    $parentFolder = $this->getOrCreateFolderByPath($path);
                    $this->createFolder($name, $folderPath, $parentFolder);
                }
                break;
            default:
                // Stack
                $parent = $this->getOrCreateFolderByPath($path);
                if ($this->getStackIDByName($name, $parent) === null) {
                    Stack::addStack($name, $parent);
                }
                break;
        }
    }
}
