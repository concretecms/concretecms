<?php
namespace Concrete\Controller\Backend\Page;

use Area;
use Block;
use Concrete\Controller\Backend\UserInterface\Page;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Config;
use Loader;
use Permissions;
use Stack;
use Concrete\Core\Http\ResponseFactoryInterface;

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

            return;
        }

        $destinationAreaID = (int) $post->get('area');
        if ($destinationAreaID === $sourceAreaID) {
            $destinationAreaHandle = $sourceAreaHandle;
            $desinationArea = $sourceArea;
        } else {
            $destinationAreaHandle = $destinationAreaID === 0 ? null : Area::getAreaHandleFromID($destinationAreaID);
            $destinationArea = (string) $destinationAreaHandle === '' ? null : Area::get($nvc, $destinationAreaHandle);
            if ($destinationArea === null) {
                $e->add(t('Unable to find the destination area.'));
                
                return;
            }
        }

        $movingBlockID = (int) $post->get('block');
        if ($movingBlockID === 0) {
            $e->add(t('Unable to find the block to be moved.'));
            
            return;
        }

        $otherBlockIDs = $post->get('blocks', []);

        if (Config::get('concrete.permissions.model') == 'advanced') {
            // first, we check to see if we have permissions to edit the area contents for the source area.
            $ap = new Permissions($sourceArea);
            if (!$ap->canEditAreaContents()) {
                $e->add(t('You may not arrange the contents of area %s.', $sourceAreaHandle));

                return;
            }
            // now we get further in. We check to see if we're dealing with both a source AND a destination area.
            // if so, we check the area permissions for the destination area.
            if ($sourceAreaID !== $destinationAreaID) {
                $destAP = new Permissions($desinationArea);
                if (!$destAP->canEditAreaContents()) {
                    $e->add(t('You may not arrange the contents of area %s.', $destinationAreaHandle));

                    return;
                }
                // we're not done yet. Now we have to check to see whether this user has permission to add
                // a block of this type to the destination area.
                if ($sourceArea->isGlobalArea()) {
                    $sourceStack = Stack::getByName($sourceAreaHandle);
                    $b = Block::getByID($movingBlockID, $sourceStack, STACKS_AREA_NAME);
                } else {
                    $b = Block::getByID($movingBlockID, $nvc, $sourceAreaHandle);
                }
                $bt = $b->getBlockTypeObject();
                if (!$destAP->canAddBlock($bt)) {
                    $e->add(t('You may not add %s to area %s.', t($bt->getBlockTypeName()), $destinationAreaHandle));

                    return;
                }
            }
            // now, if we get down here we perform the arrangement
            // it will be set to true if we're in simple permissions mode, or if we've passed all the checks
        }


        if ($sourceAreaID !== $destinationAreaID) {
            if ($destinationArea->isGlobalArea()) {
                $destinationStack = Stack::getByName($destinationAreaHandle);
                $destinationStackToModify = $destinationStack->getVersionToModify();
                $nvc->relateVersionEdits($destinationStackToModify);
                // If the source area is global, we need to get the block from there rather than from the view controller
                if ($sourceArea->isGlobalArea()) {
                    $sourceStackToModify = Stack::getByName($sourceAreaHandle)->getVersionToModify();
                    $nvc->relateVersionEdits($sourceStackToModify);
                    $block = Block::getByID($movingBlockID, $sourceStackToModify, Area::get($sourceStackToModify, STACKS_AREA_NAME));
                } else {
                    $block = Block::getByID($movingBlockID, $nvc, $sourceArea);
                }
                $block->move($destinationStackToModify, Area::get($destinationStackToModify, STACKS_AREA_NAME));
            } elseif ($sourceArea->isGlobalArea()) {
                $sourceStack = Stack::getByName($sourceAreaHandle);
                $sourceStackToModify = $sourceStack->getVersionToModify();
                $nvc->relateVersionEdits($sourceStackToModify);
                $block = Block::getByID($movingBlockID, $sourceStackToModify, Area::get($sourceStackToModify, STACKS_AREA_NAME));
                $block->move($nvc, Area::get($nvc, STACKS_AREA_NAME));
            }
        }

        if ($destinationArea->isGlobalArea()) {
            $destinationStack = Stack::getByName($destinationAreaHandle);
            $destinationStackToModify = $destinationStack->getVersionToModify();
            $actualDestinationArea = Area::get($destinationStackToModify, STACKS_AREA_NAME);
            $actualDestinationAreaID = $actualDestinationArea->getAreaID();
            $nvc->relateVersionEdits($destinationStackToModify);
            $destinationStackToModify->processArrangement($actualDestinationAreaID, $movingBlockID, $otherBlockIDs);
        } else {
            $nvc->processArrangement($destinationAreaID, $movingBlockID, $otherBlockIDs);
        }
    }
}
