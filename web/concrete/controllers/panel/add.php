<?php
namespace Concrete\Controller\Panel;

use BlockType;
use BlockTypeList;
use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Page\Stack\Pile\Pile;
use Loader;
use Session;
use StackList;

class Add extends BackendInterfacePageController
{

    protected $viewPath = '/panels/add';
    protected $pagetypes = array();

    public function view()
    {
        $btl = new BlockTypeList();
        $blockTypes = $btl->get();
        $dsh = Loader::helper('concrete/dashboard');
        $dashboardBlockTypes = array();
        if ($dsh->inDashboard()) {
            $dashboardBlockTypes = BlockTypeList::getDashboardBlockTypes();
        }
        $blockTypes = array_merge($blockTypes, $dashboardBlockTypes);
        if ($this->page->isMasterCollection()) {
            $bt = BlockType::getByHandle(BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY);
            $blockTypes[] = $bt;
        }
        if ($_REQUEST['tab']) {
            Session::set('panels_page_add_block_tab', $_REQUEST['tab']);
            $tab = $_REQUEST['tab'];
        } else {
            $tab = Session::get('panels_page_add_block_tab');
        }
        $sp = Pile::getDefault();
        $contents = $sp->getPileContentObjects('date_desc');

        $stacks = new StackList();
        $stacks->filterByUserAdded();

        $this->set('stacks', $stacks->get());
        $this->set('contents', $contents);
        $this->set('tab', $tab);
        $this->set('blockTypes', $blockTypes);
        $this->set('ih', Loader::helper('concrete/ui'));
        $this->set('ci', Loader::helper('concrete/urls'));
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }

}

