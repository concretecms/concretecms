<?php

declare(strict_types=1);

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Exception\BlockNotFoundException;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Stack\Stack;

defined('C5_EXECUTE') or die('Access Denied.');

class SortAreaBlocksCommandHandler
{
    /**
     * @throws \Concrete\Core\Block\Exception\BlockNotFoundException if a block is not in the area
     * @throws \InvalidArgumentException if the received block IDs aren't all and only the blocks of the area
     *
     * @return \Concrete\Core\Page\Collection\Collection the collection whose blocks have been sorted
     */
    public function __invoke(SortAreaBlocksCommand $command)
    {
        $page = $command->getPage();
        $area = $command->getArea();

        $pageToModify = $page;
        $areaHandleToModify = $area->getAreaHandle();
        if ($area->isGlobalArea()) {
            $pageToModify = Stack::getByName($area->getAreaHandle());
            $areaHandleToModify = STACKS_AREA_NAME;
        }

        $blockIDs = $command->getBlockIDs();
        // let's check the received block IDs before creating a new version of the page
        $this->checkBlockIDs($pageToModify, $areaHandleToModify, $blockIDs);

        $nvc = $pageToModify->getVersionToModify();
        $blocks = [];
        foreach ($blockIDs as $blockID) {
            $block = Block::getByID($blockID, $nvc, $areaHandleToModify);
            if (!$block instanceof Block) {
                throw new BlockNotFoundException();
            }
            $blocks[] = $block;
        }
        // let's move the blocks only when we are sure that we can move all of them
        foreach ($blocks as $displayOrder => $block) {
            $block->setAbsoluteBlockDisplayOrder($displayOrder);
        }

        if ($area->isGlobalArea()) {
            $xvc = $page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        return $nvc;
    }

    /**
     * Check that the received block IDs are all and only the IDs of the blocks currently in the area.
     *
     * @param int[] $blockIDs
     *
     * @throws \InvalidArgumentException
     */
    private function checkBlockIDs(Collection $collection, string $arHandle, array $blockIDs): void
    {
        $existingBlockIDs = [];
        foreach ($collection->getBlockIDs($arHandle) as $row) {
            $existingBlockIDs[] = (int) $row['bID'];
        }
        if (count($blockIDs) !== count(array_unique($blockIDs))
            || array_diff($existingBlockIDs, $blockIDs) !== []
            || array_diff($blockIDs, $existingBlockIDs) !== []
        ) {
            throw new \InvalidArgumentException(
                t('The blocks to be sorted must be all and only the blocks currently in the area.')
            );
        }
    }
}
