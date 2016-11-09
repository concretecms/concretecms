<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Events\BlockEdit;
use Concrete\Core\Block\View\BlockView;
use BlockType;
use Area;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Page\Page;
use Concrete\Core\View\View;
use Events;

class Edit extends BackendInterfaceBlockController
{
    protected $viewPath = '/dialogs/block/edit';

    public function view()
    {
        $bv = new BlockView($this->block);
        if (isset($_REQUEST['arGridMaximumColumns'])) {
            $this->area->setAreaGridMaximumColumns(intval($_REQUEST['arGridMaximumColumns']));
        }
        if (isset($_REQUEST['arEnableGridContainer']) && $_REQUEST['arEnableGridContainer'] == 1) {
            $this->area->enableGridContainer();
        }
        $bv->addScopeItems(array('c' => $this->page, 'a' => $this->area, 'dialogController' => $this));
        $this->set('bv', $bv);
    }

    protected function validateBlock($b)
    {
        $bi = $b->getInstance();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
            $_b = \Block::getByID($bi->getOriginalBlockID());
            $bi = $_b->getInstance(); // for validation
        }

        $e = $bi->validate($_POST);
        return $e;
    }


    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {
            $b = $this->getBlockToEdit();
            $e = $this->validateBlock($b);
            $pr = $this->getEditResponse($b, $e);

            if (!is_object($e) || ($e instanceof \Concrete\Core\Error\ErrorList\ErrorList && !$e->has())) {
                // we can update the block that we're submitting
                $b->update($_POST);
                $event = new BlockEdit($b, $this->page);
                Events::dispatch('on_block_edit', $event);
            }

            $pr->outputJSON();
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlock();
    }
}
