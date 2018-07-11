<?php

namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\StackList;

class Add extends BackendInterfacePageController
{
    protected $viewPath = '/panels/add';
    protected $pagetypes = [];

    public function view()
    {
        $requestTab = $this->request('tab');
        $session = $this->app->make('session');
        if ($requestTab) {
            $session->set('panels_page_add_block_tab', $requestTab);
            $tab = $requestTab;
        } else {
            $tab = $session->get('panels_page_add_block_tab');
        }
        switch ($tab) {
            case 'stacks':
                $stacks = new StackList();
                $stacks->filterByUserAdded();
                $this->set('stacks', $stacks->getResults());
                break;
            case 'clipboard':
                $sp = Pile::getDefault();
                $contents = $sp->getPileContentObjects('date_desc');
                $this->set('contents', $contents);
                break;
            case 'blocks':
            default:
                $tab = 'blocks';
                $btl = new BlockTypeList();
                $blockTypes = $btl->get();
                if ($this->page->isMasterCollection()) {
                    $bt = BlockType::getByHandle(BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY);
                    $blockTypes[] = $bt;
                }
                $this->set('blockTypes', $blockTypes);
                $dsh = $this->app->make('helper/concrete/dashboard');
                if ($dsh->inDashboard() || strpos($this->page->getCollectionPath(), '/account') === 0) {
                    $sets = BlockTypeSet::getList([]);
                } else {
                    $sets = BlockTypeSet::getList();
                }
                $this->set('sets', $sets);
                break;
        }
        $this->set('tab', $tab);
        $this->set('ci', $this->app->make('helper/concrete/urls'));
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }
}
