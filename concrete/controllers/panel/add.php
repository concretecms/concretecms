<?php

namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Support\Facade\StackFolder;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;

class Add extends BackendInterfacePageController
{
    protected $viewPath = '/panels/add';
    protected $pagetypes = [];

    public function view()
    {
        $tab = $this->getSelectedTab();
        switch ($tab) {
            case 'containers':
                $theme = $this->page->getCollectionThemeObject();
                $containers = [];
                if ($theme) {
                    $containers = $this->app->make(EntityManager::class)
                        ->getRepository(Container::class)->findAll();
                }
                $this->set('containers', $containers);
            case 'stacks':
                $parent = Page::getByPath(STACKS_PAGE_PATH);
                $list = new StackList();
                $list->filterByParentID($parent->getCollectionID());
                $list->setFoldersFirst(true);
                $list->excludeGlobalAreas();
                $stacks = $list->getResults();
                $this->set('stacks', $stacks);
                break;
            case 'clipboard':
                $sp = Pile::getDefault();
                $contents = $sp->getPileContentObjects('date_desc');
                $this->set('contents', $contents);
                break;
            case 'blocks':
            default:
                $tab = 'blocks';
                $this->set('blockTypesForSets', $this->buildSetsAndBlockTypes());
                break;
        }
        $this->set('tab', $tab);
        $this->set('ci', $this->app->make('helper/concrete/urls'));
    }

    public function getStackFolderContents()
    {
        $this->set('ci', $this->app->make('helper/concrete/urls'));
        $stackFolder = StackFolder::getByID($this->request->request->get('stackFolderID'));
        if (is_object($stackFolder)) {
            $list = new StackList();
            $list->filterByFolder($stackFolder);
            $list->setFoldersFirst(true);
            $stacks = $list->getResults();
            $this->set('stacks', $stacks);
            $this->setViewObject(new View('/panels/add/get_stack_folder_contents'));
            return;
        }
        throw new \Exception(t('Access Denied.'));
    }

    public function getStackContents()
    {
        $this->set('ci', $this->app->make('helper/concrete/urls'));
        $stack = Stack::getByID($this->request->request->get('stackID'));
        if ($stack && !$stack->isError()) {
            $sp = new \Permissions($stack);
            if ($sp->canViewPage()) {
                $blocks = $stack->getBlocks();
                $this->set('blocks', $blocks);
                $this->set('stack', $stack);
                $this->setViewObject(new View('/panels/add/get_stack_contents'));
                return;
            }
        }
        throw new \Exception(t('Access Denied.'));
    }

    protected function getSelectedTab()
    {
        $requestTab = $this->request('tab');
        $session = $this->app->make('session');
        if ($requestTab) {
            $session->set('panels_page_add_block_tab', $requestTab);
            $tab = $requestTab;
        } else {
            $tab = $session->get('panels_page_add_block_tab');
        }

        return $tab;
    }

    protected function canAccess()
    {
        return $this->permissions->canEditPageContents();
    }

    /**
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType[] array keys are the set names, array values are the block types associated to those sets
     */
    protected function buildSetsAndBlockTypes()
    {
        $allowedBlockTypes = [];
        $btl = new BlockTypeList();
        foreach ($btl->get() as $blockType) {
            if ($this->permissions->canAddBlockType($blockType)) {
                $allowedBlockTypes[] = $blockType;
            }
        }
        if ($this->page->isMasterCollection()) {
            $allowedBlockTypes[] = BlockType::getByHandle(BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY);
        }
        $dsh = $this->app->make('helper/concrete/dashboard');
        if ($dsh->inDashboard() || strpos($this->page->getCollectionPath(), '/account') === 0) {
            $sets = BlockTypeSet::getList([]);
        } else {
            $sets = BlockTypeSet::getList();
        }
        $remainingBlockTypes = $allowedBlockTypes;
        $blockTypesForSets = [];
        foreach ($sets as $set) {
            $blockTypesForSet = [];
            foreach ($set->getBlockTypes() as $blockType) {
                if (in_array($blockType, $allowedBlockTypes, true)) {
                    $blockTypesForSet[] = $blockType;
                    $i = array_search($blockType, $remainingBlockTypes, true);
                    if ($i !== false) {
                        unset($remainingBlockTypes[$i]);
                    }
                }
            }
            if (!empty($blockTypesForSet)) {
                $key = $set->getBlockTypeSetDisplayName();
                if (isset($blockTypesForSets[$key])) {
                    $blockTypesForSets[$key] = array_merge($blockTypesForSets[$key], $blockTypesForSet);
                } else {
                    $blockTypesForSets[$key] = $blockTypesForSet;
                }
            }
        }
        if (!empty($remainingBlockTypes)) {
            $blockTypesForSet = [];
            foreach (BlockTypeSet::getUnassignedBlockTypes(true) as $blockType) {
                if (in_array($blockType, $remainingBlockTypes, true)) {
                    $blockTypesForSet[] = $blockType;
                }
            }
            if (!empty($blockTypesForSet)) {
                $key = t('Other');
                if (isset($blockTypesForSets[$key])) {
                    $blockTypesForSets[$key] = array_merge($blockTypesForSets[$key], $blockTypesForSet);
                } else {
                    $blockTypesForSets[$key] = $blockTypesForSet;
                }
            }
        }

        return $blockTypesForSets;
    }
}
