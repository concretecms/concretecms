<?php
namespace Concrete\Controller\Panel;

use BlockType;
use BlockTypeList;
use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Page\Stack\Pile\Pile;
use StackList;

class Add extends BackendInterfacePageController
{
    protected $viewPath = '/panels/add';
    protected $pagetypes = array();

    public function view()
    {
        $btl = new BlockTypeList();
        $blockTypes = $btl->get();
        $dsh = $this->app->make('helper/concrete/dashboard');
        if ($this->page->isMasterCollection()) {
            $bt = BlockType::getByHandle(BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY);
            $blockTypes[] = $bt;
        }

        $requestTab = $this->request('tab');
        $session = $this->app->make('session');
        if ($requestTab) {

            $session->set('panels_page_add_block_tab', $requestTab);
            $tab = $requestTab;
        } else {
            $tab = $session->get('panels_page_add_block_tab');
        }

        $sp = (new Pile())->getDefault();
        $contents = $sp->getPileContentObjects('date_desc');

        $stacks = new StackList();
        //if (\Core::make('multilingual/detector')->isEnabled()) {
        //    $stacks->filterByPageLanguage($this->page);
        //}
        $stacks->filterByUserAdded();

        if ($dsh->inDashboard() || strpos($this->page->getCollectionPath(), '/account') === 0) {
            $sets = Set::getList(array());
        } else {
            $sets = Set::getList();
        }

        $this->set('stacks', $stacks->getResults());
        $this->set('contents', $contents);
        $this->set('tab', $tab);
        $this->set('blockTypes', $blockTypes);
        $this->set('sets', $sets);
        $this->set('ih', $this->app->make('helper/concrete/ui'));
        $this->set('ci',$this->app->make('helper/concrete/urls'));
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }
}
