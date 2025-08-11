<?php

namespace Concrete\Core\Export\Item;

use Concrete\Core\Page\Stack\Folder\Folder as FolderObject;
use Concrete\Core\Page\Stack\Folder\FolderService;
use SimpleXMLElement;

defined('C5_EXECUTE') or die("Access Denied.");

class StackFolder implements ItemInterface
{
    /**
     * @param \Concrete\Core\Page\Stack\Folder\Folder $folder
     *
     * @return \SimpleXMLElement
     */
    public function export($folder, SimpleXMLElement $xml)
    {
        $folders = $this->expandFolders($folder);
        $path = '';
        foreach ($folders as $folder) {
            $folderPage = $folder->getPage();
            $name = $folderPage->getCollectionName();
            $this->ensureFolder($xml, $name, $path ?: '/');
            $path .= '/' . $name;
        }
    }

    /**
     * @return \Concrete\Core\Page\Stack\Folder\Folder[]
     */
    private function expandFolders(FolderObject $folder)
    {
        $service = app(FolderService::class);
        $result = [];
        while ($folder) {
            $result[] = $folder;
            $parentID = $folder->getPage()->getCollectionParentID();
            $folder = $parentID ? $service->getByID($parentID) : null;
        }
        return array_reverse($result);
    }

    /**
     * @param string $name
     * @param string $path
     */
    private function ensureFolder(SimpleXMLElement $parentElement, $name, $path)
    {
        foreach ($parentElement->folder as $existing) {
            if ($name === (string) $existing['name'] && $path === (string) $existing['path']) {
                return;
            }
        }
        $el = $parentElement->addChild('folder');
        $el->addAttribute('name', $name);
        $el->addAttribute('path', $path);
    }
}
