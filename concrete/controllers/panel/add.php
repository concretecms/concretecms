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
        $tab = $this->getSelectedTab();
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
                $this->set('blockTypesForSets', $this->buildSetsAndBlockTypes());
                break;
        }
        $this->set('tab', $tab);
        $this->set('ci', $this->app->make('helper/concrete/urls'));
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
