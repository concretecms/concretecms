<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Controller\Backend\UserInterface\Page;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Permission\Checker;

class ArrangeBlocks extends Page
{
    public function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }

    public function arrange()
    {
        $pc = new PageEditResponse();
        $pc->setPage($this->page);
        $this->performArrangement($pc);

        return $this->app->make(ResponseFactoryInterface::class)->json($pc);
    }

    /**
     * @param \Concrete\Core\Page\EditResponse $pc
     */
    protected function performArrangement(PageEditResponse $pc)
    {
        $e = $pc->getError();
        $post = $this->request->request;
        $nvc = $this->page->getVersionToModify();

        $sourceAreaID = (int) $post->get('sourceArea');
        $sourceAreaHandle = $sourceAreaID === 0 ? null : Area::getAreaHandleFromID($sourceAreaID);
        $sourceArea = (string) $sourceAreaHandle === '' ? null : Area::get($nvc, $sourceAreaHandle);
        if ($sourceArea === null) {
            $e->add(t('Unable to find the source area.'));
        }

        $destinationAreaID = (int) $post->get('area');
        if ($destinationAreaID === $sourceAreaID) {
            $destinationAreaHandle = $sourceAreaHandle;
            $destinationArea = $sourceArea;
        } else {
            $destinationAreaHandle = $destinationAreaID === 0 ? null : Area::getAreaHandleFromID($destinationAreaID);
            $destinationArea = (string) $destinationAreaHandle === '' ? null : Area::get($nvc, $destinationAreaHandle);
            if ($destinationArea === null) {
                $e->add(t('Unable to find the destination area.'));
            }
        }

        $movingBlockID = (int) $post->get('block');

        if ($movingBlockID === 0) {
            $e->add(t('Unable to find the block to be moved.'));
        }

        $sortedBlockIDs = $post->get('blocks', []);
        if (is_array($sortedBlockIDs)) {
            $sortedBlockIDs = array_values(array_filter(array_map('intval', $sortedBlockIDs)));
        } else {
            $sortedBlockIDs = [];
        }
        if (!in_array($movingBlockID, $sortedBlockIDs, true)) {
            $e->add(t('Unable to find the block to be moved.'));
        }
        if ($e->has()) {
            return;
        }
        if ($this->app->make('config')->get('concrete.permissions.model') == 'advanced') {
            // first, we check to see if we have permissions to edit the area contents for the source area.
            $ap = new Checker($sourceArea);
            if (!$ap->canEditAreaContents()) {
                $e->add(t('You may not arrange the contents of area %s.', $sourceAreaHandle));
            }
            // now we get further in. We check to see if we're dealing with both a source AND a destination area.
            // if so, we check the area permissions for the destination area.
            if ($sourceAreaID !== $destinationAreaID) {
                $destAP = new Checker($destinationArea);
                if (!$destAP->canEditAreaContents()) {
                    $e->add(t('You may not arrange the contents of area %s.', $destinationAreaHandle));
                }
                // we're not done yet. Now we have to check to see whether this user has permission to add
                // a block of this type to the destination area.
                if ($sourceArea->isGlobalArea()) {
                    $sourceStack = Stack::getByName($sourceAreaHandle);
                    $block = Block::getByID($movingBlockID, $sourceStack, STACKS_AREA_NAME);
                } else {
                    $block = Block::getByID($movingBlockID, $nvc, $sourceAreaHandle);
                }
                // we need to check permissions of the original block in case of a scrapbook proxy
                if ($block && $block->getBlockTypeHandle() === BLOCK_HANDLE_SCRAPBOOK_PROXY) {
                    $block = Block::getByID($block->getController()->getOriginalBlockID());
                }
                if (!$block) {
                    $e->add(t('Unable to find the block to be moved.'));
                } elseif (!$destAP->canAddBlock($block)) {
                    $e->add(t('You may not add %s to area %s.', t($block->getBlockTypeObject()->getBlockTypeName()), $destinationAreaHandle));
                }
            }
            if ($e->has()) {
                return;
            }
            // now, if we get down here we perform the arrangement
            // it will be set to true if we're in simple permissions mode, or if we've passed all the checks
        }

        if ($destinationArea->isGlobalArea()) {
            $destinationStack = Stack::getByName($destinationAreaHandle);
            $destinationStackToModify = $destinationStack->getVersionToModify();
            $actualDestinationArea = Area::get($destinationStackToModify, STACKS_AREA_NAME);
            $actualDestinationAreaID = $actualDestinationArea->getAreaID();
            if ($sourceAreaID !== $destinationAreaID) {
                $nvc->relateVersionEdits($destinationStackToModify);
                // If the source area is global, we need to get the block from there rather than from the view controller
                if ($sourceArea->isGlobalArea()) {
                    $sourceStack = Stack::getByName($sourceAreaHandle);
                    $sourceStackToModify = $sourceStack->getVersionToModify();
                    $nvc->relateVersionEdits($sourceStackToModify);
                    $block = Block::getByID($movingBlockID, $sourceStackToModify, Area::get($sourceStackToModify, STACKS_AREA_NAME));
                } else {
                    $block = Block::getByID($movingBlockID, $nvc, $sourceArea);
                }
                if (!$block) {
                    $e->add(t('Unable to find the block to be moved.'));

                    return;
                }
                $block->move($destinationStackToModify, $actualDestinationArea);
            }
            $nvc->relateVersionEdits($destinationStackToModify);
            $destinationStackToModify->processArrangement($actualDestinationAreaID, $movingBlockID, $sortedBlockIDs);
        } else {
            if ($sourceAreaID !== $destinationAreaID && $sourceArea->isGlobalArea()) {
                $sourceStack = Stack::getByName($sourceAreaHandle);
                $sourceStackToModify = $sourceStack->getVersionToModify();
                $block = Block::getByID($movingBlockID, $sourceStackToModify, Area::get($sourceStackToModify, STACKS_AREA_NAME));
                if (!$block) {
                    $e->add(t('Unable to find the block to be moved.'));

                    return;
                }
                $nvc->relateVersionEdits($sourceStackToModify);
                $block->move($nvc, Area::get($nvc, STACKS_AREA_NAME));
            }
            $nvc->processArrangement($destinationAreaID, $movingBlockID, $sortedBlockIDs);
        }
    }
}
