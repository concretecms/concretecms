<?php

namespace Concrete\Core\Export\Item;

use Concrete\Core\Area\Area as AreaObject;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Multilingual\Page\Section\Section as SectionObject;
use Concrete\Core\Page\Stack\Folder\FolderService;
use Concrete\Core\Page\Stack\Stack as StackObject;
use SimpleXMLElement;

defined('C5_EXECUTE') or die("Access Denied.");

class Stack implements ItemInterface
{
    /**
     * @param \Concrete\Core\Page\Stack\Stack $stack
     *
     * {@inheritDoc}
     * @see \Concrete\Core\Export\Item\ItemInterface::export()
     */
    public function export($stack, SimpleXMLElement $xml)
    {
        if (!$stack->isNeutralStack()) {
            return [];
        }
        $path = $this->buildStackPath($stack);
        $newNodes = [
            $this->exportStack($xml, $stack, $path)
        ];
        $sections = SectionObject::getList($stack->getSite());
        foreach ($sections as $section) {
            $localizedStack = $stack->getLocalizedStack($section);
            if ($localizedStack !== null) {
                $newNodes[] = $this->exportStack($xml, $localizedStack, $path, $section);
            }
        }

        return $newNodes;
    }

    /**
     * @return string
     */
    private function buildStackPath(StackObject $stack)
    {
        if ($stack->getStackType() === StackObject::ST_TYPE_GLOBAL_AREA) {
            return '/';
        }
        $slugs = [];
        $page = $stack;
        $service = app(FolderService::class);
        while (true) {
            $folder = $service->getByID($page->getCollectionParentID());
            if (!$folder) {
                break;
            }
            $page = $folder->getPage();
            $slugs[] = $page->getCollectionName();
        }

        return '/' . implode('/', array_reverse($slugs));
    }

    /**
     * @param string $path
     *
     * @return \SimpleXMLElement
     */
    private function exportStack(SimpleXMLElement $xml, StackObject $stack, $path, ?SectionObject $section = null)
    {
        $db = app(Connection::class);
        $node = $xml->addChild('stack');
        $node->addAttribute('name', $stack->getCollectionName());
        $type = $stack->getStackTypeExportText();
        if ($type) {
            $node->addAttribute('type', $type);
        }
        $node->addAttribute('path', $path);
        if ($section !== null) {
            $node->addAttribute('section', $section->getLocale());
        }

        // We should have just a 'Main' area in stacks, but just in case.
        $r = $db->executeQuery('select arHandle from Areas where cID = ? and arParentID = 0', [$stack->getCollectionID()]);
        while (($row = $r->fetch()) !== false) {
            $ax = AreaObject::get($stack, $row['arHandle']);
            if ($ax) {
                $ax->export($node, $stack);
            }
        }

        return $node;
    }
}
