<?php
namespace Concrete\Controller\Backend;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\View\DialogView;
use Core;
use Localization;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block extends BackendInterfaceBlockController
{
    public function render()
    {
        $loc = Localization::getInstance();
        $loc->setActiveContext('site');

        $c = $this->block->getBlockCollectionObject();
        $dl = Core::make('multilingual/detector');
        $dl->setupSiteInterfaceLocalization($this->page);

        $btc = $this->block->getInstance();
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $this->area->forceControlsToDisplay(); // we always want to show them controls.
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
        $this->view = new DialogView('/backend/block');
    }

    public function delete()
    {
        $valt = \Core::make('token');
        if ($valt->validate()) {
            if (is_object($this->area)) {
                $ax = $this->area;
                $cx = $this->page;
                if ($this->area->isGlobalArea()) {
                    $ax = STACKS_AREA_NAME;
                    $cx = \Stack::getByName($_REQUEST['arHandle']);
                }

                $b = \Block::getByID($_REQUEST['bID'], $cx, $ax);
                $p = new \Permissions($b); // might be block-level, or it might be area level
                // we're removing a particular block of content
                if ($p->canDeleteBlock()) {
                    $nvc = $cx->getVersionToModify();

                    if ($this->area->isGlobalArea()) {
                        $xvc = $this->page->getVersionToModify(); // we need to create a new version of THIS page as well.
                        $xvc->relateVersionEdits($nvc);
                    }

                    $b->loadNewCollection($nvc);

                    $b->deleteBlock();

                    $event = new BlockDelete($b, $this->page);
                    \Events::dispatch('on_block_delete', $event);

                    $nvc->rescanDisplayOrder($_REQUEST['arHandle']);

                    return new JsonResponse(array());
                }
            }
        }
    }


    protected function canAccess()
    {
        return $this->permissions->canViewEditInterface();
    }
}
