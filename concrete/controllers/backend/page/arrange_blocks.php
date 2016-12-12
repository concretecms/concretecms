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
        $e = Loader::helper('validation/error');

        $nvc = $this->page->getVersionToModify();
        $sourceAreaID = intval($_POST['sourceArea']);
        $destinationAreaID = intval($_POST['area']);
        $affectedAreaIDs = array();
        $affectedAreaIDs[] = $sourceAreaID;
        if ($sourceAreaID != $destinationAreaID) {
            $affectedAreaIDs[] = $destinationAreaID;
        }

        if (Config::get('concrete.permissions.model') == 'advanced') {
            // first, we check to see if we have permissions to edit the area contents for the source area.
            $arHandle = Area::getAreaHandleFromID($sourceAreaID);
            $ar = Area::getOrCreate($nvc, $arHandle);
            $ap = new Permissions($ar);
            if (!$ap->canEditAreaContents()) {
                $e->add(t('You may not arrange the contents of area %s.', $arHandle));
            } else {
                // now we get further in. We check to see if we're dealing with both a source AND a destination area.
                // if so, we check the area permissions for the destination area.
                if ($sourceAreaID != $destinationAreaID) {
                    $destAreaHandle = Area::getAreaHandleFromID($destinationAreaID);
                    $destArea = Area::getOrCreate($nvc, $destAreaHandle);
                    $destAP = new Permissions($destArea);
                    if (!$destAP->canEditAreaContents()) {
                        $e->add(t('You may not arrange the contents of area %s.', $destAreaHandle));
                    } else {
                        // we're not done yet. Now we have to check to see whether this user has permission to add
                        // a block of this type to the destination area.

                        if ($ar->isGlobalArea()) {
                            $stack = Stack::getByName($arHandle);
                            $b = Block::getByID($_REQUEST['block'], $stack, STACKS_AREA_NAME);
                        } else {
                            $b = Block::getByID($_REQUEST['block'], $nvc, $arHandle);
                        }
                        $bt = $b->getBlockTypeObject();
                        if (!$destAP->canAddBlock($bt)) {
                            $e->add(t('You may not add %s to area %s.', t($bt->getBlockTypeName()), $destAreaHandle));
                        }
                    }
                }
            }

            // now, if we get down here we perform the arrangement
            // it will be set to true if we're in simple permissions mode, or if we've passed all the checks
        }

        $source_area = Area::get($nvc, Area::getAreaHandleFromID($sourceAreaID));
        $destination_area = Area::get($this->page, Area::getAreaHandleFromID($destinationAreaID));

        if ($sourceAreaID !== $destinationAreaID &&
            ($source_area->isGlobalArea() || $destination_area->isGlobalArea())
        ) {

            // If the source_area is the only global area
            if ($source_area->isGlobalArea() && !$destination_area->isGlobalArea()) {
                $cp = new Permissions($nvc);
                $stack = Stack::getByName($source_area->getAreaHandle());
                $stackToModify = $stack->getVersionToModify();
                $nvc->relateVersionEdits($stackToModify);
                $block = Block::getByID($_POST['block'], $stackToModify, Area::get($stackToModify, STACKS_AREA_NAME));
                $block->move($nvc, Area::get($nvc, STACKS_AREA_NAME));
            }

            if ($destination_area->isGlobalArea()) {
                $cp = new Permissions($nvc);
                $stack = Stack::getByName($destination_area->getAreaHandle());
                $stackToModify = $stack->getVersionToModify();
                $nvc->relateVersionEdits($stackToModify);
                // If the source area is global, we need to get the block from there rather than from the view controller
                if ($source_area->isGlobalArea()) {
                    $sourceStackToModify = Stack::getByName($source_area->getAreaHandle())->getVersionToModify();
                    $nvc->relateVersionEdits($sourceStackToModify);
                    $block = Block::getByID($_POST['block'], $sourceStackToModify, Area::get($sourceStackToModify, STACKS_AREA_NAME));
                } else {
                    $block = Block::getByID($_POST['block'], $nvc, $source_area);
                }
                $block->move($stackToModify, Area::get($stackToModify, STACKS_AREA_NAME));
            }
        }

        if (!$e->has()) {

            $request = \Request::getInstance();
            $area_id = $request->post('area', 0);
            $block_id = $request->post('block', 0);
            $block_ids = $request->post('blocks', array());

            if ($destination_area->isGlobalArea()) {
                $stack = Stack::getByName($destination_area->getAreaHandle());
                $stackToModify = $stack->getVersionToModify();
                $area = Area::get($stackToModify, STACKS_AREA_NAME);
                $area_id = $area->getAreaID();
                $nvc->relateVersionEdits($stackToModify);
                $stackToModify->processArrangement($area_id, $block_id, $block_ids);
            } else {
                $nvc->processArrangement($area_id, $block_id, $block_ids);
            }
        }

        $pc->setError($e);
        $pc->outputJSON();
        exit;
    }
}

