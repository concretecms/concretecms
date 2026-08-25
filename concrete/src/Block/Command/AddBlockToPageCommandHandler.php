<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Events\BlockAdd;
use Concrete\Core\Block\Exception\BlockNotFoundException;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Stack\Stack;

class AddBlockToPageCommandHandler
{

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(AddBlockToPageCommand $command)
    {
        $page = $command->getPage();
        $area = $command->getArea();
        $blockType = $command->getBlockType();
        $data = $command->getData();
        $saveMode = $command->getSaveMode();

        $pageToModify = $page;
        $areaToModify = $area;
        if ($area->isGlobalArea()) {
            $pageToModify = Stack::getByName($area->getAreaHandle());
            $areaToModify = Area::get($pageToModify, STACKS_AREA_NAME);
        }

        $beforeBlock = $command->getBeforeBlock();
        $moveAfterBlock = null;
        if ($beforeBlock !== null) {
            // let's resolve it now, that is before creating a new version of the page (and the new block itself)
            $moveAfterBlock = $this->getPreviousBlock($pageToModify, $areaToModify, $beforeBlock);
        }

        if (!$blockType->includeAll()) {
            $nvc = $pageToModify->getVersionToModify();
            $nb = $nvc->addBlock($blockType, $areaToModify, $data, $saveMode);
        } else {
            // if we apply to all, then we don't worry about a new version of the page
            $nb = $pageToModify->addBlock($blockType, $areaToModify, $data, $saveMode);
        }

        if ($beforeBlock !== null) {
            $nb->moveBlockToDisplayOrderPosition($moveAfterBlock);
        }

        $event = new BlockAdd($nb, $pageToModify);
        $this->dispatcher->dispatch('on_block_add', $event);

        if ($area->isGlobalArea() && $nvc instanceof Collection) {
            $xvc = $page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        return $nb;
    }

    /**
     * Get the block that comes just before another block in a page area (NULL if it's the first one).
     *
     * @param \Concrete\Core\Area\Area|string $area
     *
     * @throws \Concrete\Core\Block\Exception\BlockNotFoundException if $beforeBlock is not in that page area
     *
     * @return \Concrete\Core\Block\Block|null NULL if $beforeBlock is the first block of the area
     */
    private function getPreviousBlock(Collection $collection, $area, Block $beforeBlock): ?Block
    {
        $arHandle = is_object($area) ? $area->getAreaHandle() : $area;
        $previousBlockID = null;
        foreach ($collection->getBlockIDs($arHandle) as $row) {
            if ((int) $row['bID'] === (int) $beforeBlock->getBlockID()) {
                return $previousBlockID === null ? null : Block::getByID($previousBlockID, $collection, $area);
            }
            $previousBlockID = $row['bID'];
        }

        throw new BlockNotFoundException();
    }
}
