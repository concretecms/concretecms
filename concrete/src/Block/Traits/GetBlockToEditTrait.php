<?php

namespace Concrete\Core\Block\Traits;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Area\Exception\AreaNotFoundException;
use Concrete\Core\Block\Exception\BlockNotFoundException;

/**
 * A port of \Concrete\Controller\Backend\UserInterface\Block::getBlockToEdit functionality that can be
 * more easily included in different situations
 */
trait GetBlockToEditTrait
{

    /**
     * Given a page, area handle and block ID, retrieves a block to work with. This function exists
     * because if the area in question is global we have to retrieve a block differently and set
     * different data on it.
     *
     * @param Page $page
     * @param string $areaHandle
     * @param $blockID
     * @return array
     */
    public function getBlockToWorkWith(Page $page, string $areaHandle, $blockID): array
    {
        $area = Area::get($page, $areaHandle);
        if (!is_object($area)) {
            throw new AreaNotFoundException();
        }

        if (!$area->isGlobalArea()) {
            $b = Block::getByID($blockID, $page, $area);
        } else {
            $stack = Stack::getByName($areaHandle);
            $sc = Page::getByID($stack->getCollectionID(), 'RECENT');
            $b = Block::getByID($blockID, $sc, STACKS_AREA_NAME);
            if ($b) {
                $b->setBlockAreaObject($area); // set the original area object
            }
        }

        if (!$b) {
            throw new BlockNotFoundException();
        }

        return [$area, $b];
    }

    /**
     * Given a block we do things like ensure the proper version of the page for that block is loaded
     * (and sometimes create a new version); we relate edits to pages if the block is in a stack, etc...
     * @param Block $block
     * @return Block
     */
    public function getBlockToEdit(Page $page, Area $area, string $areaHandle, $blockID): ?Block
    {
        $ax = $area;
        $cx = $page;
        if ($area->isGlobalArea()) {
            $ax = STACKS_AREA_NAME;
            $cx = Stack::getByName($areaHandle);
        }

        $nvc = $cx->getVersionToModify();
        if ($area->isGlobalArea()) {
            $xvc = $page->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }

        $b = Block::getByID($blockID, $nvc, $ax);

        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $originalDisplayOrder = $b->getBlockDisplayOrder();
            $cnt = $b->getController();
            $ob = Block::getByID($cnt->getOriginalBlockID());
            $ob->loadNewCollection($nvc);
            if (!is_object($ax)) {
                $ax = Area::getOrCreate($cx, $ax);
            }
            $ob->setBlockAreaObject($ax);
            $nb = $ob->duplicate($nvc);
            $nb->setAbsoluteBlockDisplayOrder($originalDisplayOrder);
            $b->deleteBlock();
            $b = &$nb;
        } else {
            if ($b->isAlias()) {

                // then this means that the block we're updating is an alias. If you update an alias, you're actually going
                // to duplicate the original block, and update the newly created block. If you update an original, your changes
                // propagate to the aliases
                $nb = $b->duplicate($nvc);
                $b->deleteBlock();
                $b = $nb;
            }
        }

        return $b;
    }

}
