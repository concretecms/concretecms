<?php

namespace Concrete\Controller\Panel;

use Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Application\Service\Urls;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Block\BlockType\Set as BlockTypeSet;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\StackFolder;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;

class Add extends BackendInterfacePageController
{
    protected $viewPath = '/panels/add';
    protected $pagetypes = [];
    /** @var Page */
    protected $page;

    /**
     * Get a collection of all orphaned blocks used on this page.
     *
     * @return array
     * @noinspection PhpDocSignatureInspection
     */
    private function getOrphanedBlockIds($usedAreas)
    {
        $orphanedBlockIds = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        /*
         * Get all areas from database for the current page.
         */

        $availableAreas = [];

        $queryBuilder = $db->createQueryBuilder()
            ->select("a.arHandle")
            ->from("Areas", "a")
            ->where("a.cID = :pageId")
            ->setParameter("pageId", $this->page->getCollectionID());

        $rows = $queryBuilder->execute()->fetchAll();

        foreach ($rows as $row) {
            $availableAreas[] = $row["arHandle"];
        }

        /*
         * Calculate the orphaned areas
         */

        $orphanedAreas = [];

        foreach ($availableAreas as $availableArea) {
            if (!in_array($availableArea, $usedAreas)) {
                $orphanedAreas[] = $availableArea;
            }
        }

        if (!$orphanedAreas) {
            return [];
        }

        /*
         * Get all blocks from database for all orphaned areas of the current page.
         */

        $queryBuilder = $queryBuilder
            ->resetQueryParts()
            ->select("cvb.bID, cvb.arHandle, a.arID")
            ->from("CollectionVersionBlocks", "cvb")
            ->leftJoin("cvb", "Blocks", "b", "cvb.bID = b.bID")
            ->leftJoin("cvb", "Areas", "a", "cvb.arHandle = a.arHandle")
            ->where("cvb.cID = :pageId AND cvb.cvID = :pageVersionId")
            ->setParameter("pageId", $this->page->getCollectionID())
            ->setParameter("pageVersionId", $this->page->getVersionID());

        $orX = $queryBuilder->expr()->orX();

        foreach ($orphanedAreas as $orphanedArea) {
            $orX->add($queryBuilder->expr()->eq("cvb.arHandle", $db->quote($orphanedArea)));
        }

        $queryBuilder->andWhere($orX);

        foreach ($queryBuilder->execute()->fetchAll() as $row) {
            /*
             * Use the block id as key to prevent duplicates because of the second join statement. The "group by"
             * statement results in sql_mode=only_full_group_by” MySQL-issue and all other solutions like executing
             * sub-queries to get the area name are having a bad performance.
             */
            $orphanedBlockIds[$row["bID"]] = $row;
        }

        return $orphanedBlockIds;
    }

    public function showOrphanedBlockOption(): bool
    {
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        $usedAreas = $request->request->get("usedAreas", []);
        return count($this->getOrphanedBlockIds($usedAreas)) > 0;
    }

    public function getOrphanedBlockContents()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);

        /** @var Request $request */
        $request = $this->app->make(Request::class);

        $usedAreas = $request->request->get("usedAreas", []);

        $contents = [];

        foreach ($this->getOrphanedBlockIds($usedAreas) as $item) {
            $block = Block::getByID($item["bID"]);

            if ($block instanceof Block) {
                /** @var \Concrete\Core\Entity\Block\BlockType\BlockType $type */
                /** @noinspection DuplicatedCode */
                $type = $block->getBlockTypeObject();
                $app = Application::getFacadeApplication();
                /** @var Urls $ci */
                $ci = $app->make(Urls::class);

                $icon = $ci->getBlockTypeIconURL($type);

                ob_start();
                $bv = new BlockView($block);
                $bv->render('scrapbook');
                $blockContent = ob_get_contents();
                ob_end_clean();

                $item = array_merge(
                    $item,
                    [
                        "name" => $type->getBlockTypeName(),
                        "handle" => $type->getBlockTypeHandle(),
                        "dialogTitle" => t('Add %s', t($type->getBlockTypeName())),
                        "dialogWidth" => (int)$type->getBlockTypeInterfaceWidth(),
                        "dialogHeight" => (int)$type->getBlockTypeInterfaceHeight(),
                        "hasAddTemplate" => (int)$type->hasAddTemplate(),
                        "supportsInlineAdd" => (int)$type->supportsInlineAdd(),
                        "blockTypeId" => $type->getBlockTypeID(),
                        "draggingAvatar" => h(
                            '<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="' . $icon . '" /></div><p><span>' . t(
                                $type->getBlockTypeName()
                            ) . '</span></p>'
                        ),
                        "blockId" => (int)$block->getBlockID(),
                        "blockContent" => $blockContent
                    ]
                );

                $contents[] = $item;
            }
        }

        $curPage = (int)$this->request->request->get("curPage", 0);
        $maxItems = 10;

        return $responseFactory->json(
            [
                "displayPagination" => count($contents) > $maxItems,
                "hasPrev" => $curPage > 0,
                "hasNext" => ($curPage * $maxItems + $maxItems) < count($contents),
                "results" => array_slice($contents, $curPage * $maxItems, $maxItems)
            ]
        );
    }

    public function getClipboardContents()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);

        $sp = Pile::getDefault();

        $contents = $sp->getPileContentObjects('date_desc');

        /*
         * For the clipboard pagination a client-side pagination is enough.
         * The performance issues are not because of fetching the items from database.
         * Furthermore it's an issue related to the block view rendering.
         * Therefore all results will be fetched from the database and splitted with PHP.
         */

        $curPage = (int)$this->request->request->get("curPage", 0);
        $maxItems = 10;

        return $responseFactory->json(
            [
                "displayPagination" => count($contents) > $maxItems,
                "hasPrev" => $curPage > 0,
                "hasNext" => ($curPage * $maxItems + $maxItems) < count($contents),
                "results" => array_slice($contents, $curPage * $maxItems, $maxItems)
            ]
        );
    }

    /**
     * Deletes a single orphaned block.
     */
    public function removeOrphanedBlock()
    {
        $editResponse = new EditResponse();
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var ErrorList $errorList */
        $errorList = $this->app->make(ErrorList::class);
        /** @var Request $request */
        $request = $this->app->make(Request::class);
        /** @var Token $token */
        $token = $this->app->make(Token::class);

        if (!$request->request->has("blockId")) {
            $errorList->add(t("You need to enter a valid block id."));
        } else {
            if (!$request->request->has("ccm_token")) {
                $errorList->add(t("You need to enter a valid token"));
            } else {
                if (!$this->page instanceof Page) {
                    $errorList->add(t("You need to enter a valid page id."));
                } else {
                    $blockId = (int)$request->request->get("blockId");
                    $removeToken = $request->request->get("ccm_token");

                    if (!$token->validate('remove_orphaned_block', $removeToken)) {
                        $errorList->add($token->getErrorMessage());
                    } else {
                        $usedAreas = $request->request->get("usedAreas", []);

                        $arrOrphanedBlocks = $this->getOrphanedBlockIds($usedAreas);

                        if (count($arrOrphanedBlocks) === 0) {
                            $errorList->add(t("There are no blocks to remove."));
                        } else {
                            $orphanedBlockFound = false;

                            foreach ($this->getOrphanedBlockIds($usedAreas) as $arrOrphanedBlock) {
                                if ($blockId === (int)$arrOrphanedBlock["bID"]) {
                                    $orphanedBlockFound = true;
                                }
                            }

                            if (!$orphanedBlockFound) {
                                $errorList->add(t("The given block is not orphaned."));
                            } else {
                                $block = Block::getByID($blockId);

                                if (!$block instanceof Block) {
                                    //$errorList->add(t("Error while removing orphaned block."));
                                } else {
                                    // returns false because the area no longer exists in the theme.
                                    $block->deleteBlock(true);
                                }
                            }
                        }
                    }
                }
            }
        }

        $editResponse->setTitle(t("Block removed successfully"));
        $editResponse->setMessage(t("The orphaned block has been removed successfully."));
        $editResponse->setError($errorList);

        return $responseFactory->json($editResponse);
    }


    /**
     * Deletes all orphaned block for the given page.
     */
    public function removeOrphanedBlocks()
    {
        $editResponse = new EditResponse();
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var ErrorList $errorList */
        $errorList = $this->app->make(ErrorList::class);
        /** @var Request $request */
        $request = $this->app->make(Request::class);

        $usedAreas = $request->request->get("usedAreas", []);

        $arrOrphanedBlocks = $this->getOrphanedBlockIds($usedAreas);

        if (count($arrOrphanedBlocks) === 0) {
            $errorList->add(t("There are no blocks to remove."));
        } else {
            foreach ($this->getOrphanedBlockIds($usedAreas) as $arrOrphanedBlock) {
                $bID = (int)$arrOrphanedBlock["bID"];
                $block = Block::getByID($bID);

                if (!$block instanceof Block) {
                    $errorList->add(t("Error while removing orphaned block."));
                } else {
                    // returns false because the area no longer exists in the theme.
                    $block->deleteBlock(true);
                }
            }
        }

        $editResponse->setTitle(t("Blocks removed successfully"));
        $editResponse->setMessage(t("All blocks from the current page has been removed successfully."));
        $editResponse->setError($errorList);

        return $responseFactory->json($editResponse);
    }

    public function view()
    {
        $tab = $this->getSelectedTab();

        switch ($tab) {
            case 'containers':
                $theme = $this->page->getCollectionThemeObject();
                $containers = [];
                if ($theme) {
                    $containers = $this->app->make(EntityManager::class)
                        ->getRepository(Container::class)->findBy([], ['containerName' => 'asc']);
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
            case 'orphaned_blocks':
                break;
            case 'blocks':
            default:
                $tab = 'blocks';
                $this->set('blockTypesForSets', $this->buildSetsAndBlockTypes());
                break;
        }

        $this->set('tab', $tab);
        $this->set('ci', $this->app->make('helper/concrete/urls'));
        $this->set('showOrphanedBlockOption', $this->showOrphanedBlockOption());
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

        if (!$this->showOrphanedBlockOption() && $tab === "orphaned_blocks") {
            $tab = "blocks";
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
