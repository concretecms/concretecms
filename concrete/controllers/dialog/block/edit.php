<?php
namespace Concrete\Controller\Dialog\Block;

use Concrete\Controller\Backend\UserInterface\Block as BackendInterfaceBlockController;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\Events\BlockEdit;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Formatter\JsonFormatter;
use Concrete\Core\Http\ResponseFactoryInterface;

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
            $_b = Block::getByID($bi->getOriginalBlockID());
            $bi = $_b->getInstance(); // for validation
        }

        $e = $bi->validate($_POST);
        if ($e === true) {
            $e = null;
        }
        return $e;
    }


    public function submit()
    {
        if ($this->validateAction() && $this->canAccess()) {
            $app = Application::getFacadeApplication();

            // validate the request
            $e = $this->validateBlock($this->block);
            if ($e instanceof ErrorList && $e->has()) {
                $formatter = new JsonFormatter($e);
                $response = $formatter->asArray();
                return $app->make(ResponseFactoryInterface::class)->create(json_encode($response));
            }

            // create a new version of the block
            $b = $this->getBlockToEdit();
            $pr = $this->getEditResponse($b);
            $b->update($_POST);
            $event = new BlockEdit($b, $this->page);
            Events::dispatch('on_block_edit', $event);
            return $app->make(ResponseFactoryInterface::class)->json($pr);
        }
    }

    protected function canAccess()
    {
        return $this->permissions->canEditBlock();
    }
}
