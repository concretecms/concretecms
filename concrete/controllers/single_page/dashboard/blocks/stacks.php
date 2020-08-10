<?php

namespace Concrete\Controller\SinglePage\Dashboard\Blocks;

use Concrete\Core\Entity\Statistics\UsageTracker\StackUsageRecord;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardStacksBreadcrumbFactory;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\StackFolder;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Concrete\Core\Workflow\Request\ApproveStackRequest;
use Concrete\Core\Workflow\Request\DeletePageRequest;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use View;

class Stacks extends DashboardPageController
{
    protected $themeViewTemplate = 'full.php';

    public function view_global_areas()
    {
        $stm = new StackList();
        $stm->filterByGlobalAreas();
        $this->deliverStackList($stm);

        $factory = $this->createBreadcrumbFactory();
        $factory->setDisplayGlobalAreasLandingPage(true);
        $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject()));
    }

    public function view_details($cID, $msg = false)
    {
        $factory = $this->createBreadcrumbFactory();
        if (strpos($cID, '@') !== false) {
            list($cID, $locale) = explode('@', $cID, 2);
        } else {
            $locale = '';
        }

        $s = Stack::getByID($cID);
        if (is_object($s)) {
            $isGlobalArea = $s->getStackType() == Stack::ST_TYPE_GLOBAL_AREA;
            $factory->setDisplayGlobalAreasLandingPage($isGlobalArea);
            if ($s->isNeutralStack()) {
                $neutralStack = $s;
                $stackToEdit = $s;
            } else {
                $neutralStack = $s->getNeutralStack();
                $stackToEdit = $s;
            }
            $sections = $this->getMultilingualSections();
            $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $s, $sections, $locale));
            if (!empty($sections)) {
                if ($stackToEdit !== $neutralStack) {
                    $section = $stackToEdit->getMultilingualSection();
                    if ($section !== null) {
                        $locale = $section->getLocale();
                    }
                }
                if (!isset($sections[$locale])) {
                    $locale = '';
                }
                if ($locale !== '') {
                    $this->set('localeCode', $locale);
                    $this->set('localeName', $sections[$locale]->getLanguageText());
                    $stackToEdit = $neutralStack->getLocalizedStack($sections[$locale]);
                }
            }
            if ($stackToEdit !== null) {
                $blocks = $stackToEdit->getBlocks('Main');
                $view = View::getInstance();
                foreach ($blocks as $b1) {
                    $btc = $b1->getController();
                    // now we inject any custom template CSS and JavaScript into the header
                    if ($btc instanceof \Concrete\Core\Block\BlockController) {
                        $btc->outputAutoHeaderItems();
                    }

                    $btc->runAction('on_page_view', [$view]);
                }

                $this->addHeaderItem($stackToEdit->outputCustomStyleHeaderItems(true));
                $this->set('blocks', $blocks);
            }

            $this->set('neutralStack', $neutralStack);
            $this->set('stackToEdit', $stackToEdit);
            $this->set('isGlobalArea', $isGlobalArea);
        } else {
            $folder = StackFolder::getByID($cID);
            if (is_object($folder)) {
                $stm = new StackList();
                $stm->filterByFolder($folder);
                $this->set('currentStackFolderID', $folder->getPage()->getCollectionID());
                $this->deliverStackList($stm);
                $this->set('canMoveStacks', $this->canMoveStacks($folder));
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $folder));
            } else {
                $root = Page::getByPath(STACKS_PAGE_PATH);
                if ($root->getCollectionID() != $cID) {
                    $this->error->add(t('Invalid stack'));
                }
                $this->view();
            }
        }

        switch ($msg) {
            case 'stack_added':
                $this->set('flashMessage', t('Stack added successfully.'));
                break;
            case 'localized_stack_added':
                $this->set('flashMessage', t('Localized version of stack added successfully.'));
                break;
            case 'localized_global_area_added':
                $this->set('flashMessage', t('Localized version of global area added successfully.'));
                break;
            case 'stack_approved':
                $this->set('flashMessage', t('Stack approved successfully'));
                break;
            case 'global_area_approved':
                $this->set('flashMessage', t('Global area approved successfully'));
                break;
            case 'approve_saved':
                $this->set('flashMessage', t('Approve request saved. You must complete the approval workflow before these changes are publicly accessible.'));
                break;
            case 'stack_delete_saved':
                $this->set('flashMessage', t('Delete request saved. You must complete the delete workflow before this stack can be deleted.'));
                break;
            case 'folder_delete_saved':
                $this->set('flashMessage', t('Delete request saved. You must complete the delete workflow before this stack folder can be deleted.'));
                break;
            case 'global_area_delete_saved':
                $this->set('flashMessage', t('Delete request saved. You must complete the delete workflow before this version of the global area can be deleted.'));
                break;
            case 'rename_saved':
                $this->set('flashMessage', t('Rename request saved. You must complete the approval workflow before the name will be updated.'));
                break;
            case 'stack_deleted':
                $this->flash('success', t('Stack deleted successfully'));
                break;
            case 'global_area_cleared':
                $this->flash('success', t('Global area cleared successfully'));
                break;
            case 'localized_stack_deleted':
                $this->set('flashMessage', t('Localized version of stack deleted successfully'));
                break;
            case 'localized_global_area_deleted':
                $this->set('flashMessage', t('Localized version of global area deleted successfully'));
                break;
            case 'stack_duplicated':
                $this->set('flashMessage', t('Stack duplicated successfully'));
                break;
            case 'stack_renamed':
                $this->set('flashMessage', t('Stack renamed successfully'));
                break;
            case 'folder_renamed':
                $this->set('flashMessage', t('Stack Folder renamed successfully'));
                break;
            case 'folder_added':
                $this->set('flashMessage', t('Folder added successfully.'));
                break;
            case 'folder_deleted':
                $this->set('flashMessage', t('Folder deleted successfully.'));
                break;
        }

        $this->set('composer', $this->app->make('helper/concrete/composer'));
    }

    public function view()
    {
        //$parent = Page::getByPath(STACKS_PAGE_PATH, 'RECENT', $this->getSite());
        $parent = Page::getByPath(STACKS_PAGE_PATH);
        $stm = new StackList();
        $stm->filterByParentID($parent->getCollectionID());
        $stm->excludeGlobalAreas();
        $this->deliverStackList($stm);
        $this->set('canMoveStacks', $this->canMoveStacks($parent));
        $this->set('showGlobalAreasFolder', true);
    }

    public function add_stack()
    {
        if ($this->token->validate('add_stack')) {
            $stackName = $this->request->request->get('stackName');
            if ($this->app->make('helper/validation/strings')->notempty($stackName)) {
                $folder = null;
                $folderID = $this->request->request->get('stackFolderID');
                if ($this->app->make('helper/validation/numbers')->integer($folderID)) {
                    $folder = StackFolder::getByID($folderID);
                    if (!is_object($folder)) {
                        $this->error->add(t('Unable to find the specified stack folder.'));
                    }
                }

                if (!$this->error->has()) {
                    $stack = Stack::addStack($stackName, $folder);

                    return $this->buildRedirect($this->action('view_details', $stack->getCollectionID(), 'stack_added'));
                }
            } else {
                $this->error->add(t('You must give your stack a name.'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function add_localized_stack()
    {
        if ($this->token->validate('add_localized_stack')) {
            $neutralStack = Stack::getByID($this->post('stackID'));
            $isGlobalArea = false;
            if (!$neutralStack) {
                $this->error->add(t('Unable to find the specified stack'));
            } elseif ($neutralStack->getStackType() == Stack::ST_TYPE_GLOBAL_AREA) {
                $isGlobalArea = true;
            }
            $section = Section::getByLocale($this->post('locale'));
            if (!$section) {
                $this->error->add(t('Unable to find the specified language'));
            } elseif ($neutralStack && $neutralStack->getLocalizedStack($section) !== null) {
                if ($isGlobalArea) {
                    $this->error->add(t(// i18n %s is a language name
                            "There's already a version of this global area in %s",
                        $section->getLanguageText()
                    ) . ' (' . $section->getLocale() . ')');
                } else {
                    $this->error->add(t(// i18n %s is a language name
                            "There's already a version of this stack in %s",
                        $section->getLanguageText()
                    ) . ' (' . $section->getLocale() . ')');
                }
            }
            if ($neutralStack) {
                $cpc = new Checker($neutralStack);
                if (!$cpc->canAddSubpage()) {
                    $this->error->add(t('Access denied'));
                }
            }
            if (!$this->error->has()) {
                $neutralStack->addLocalizedStack($section);

                return $this->buildRedirect($this->action(
                    'view_details',
                    $neutralStack->getCollectionID() . rawurlencode('@' . $section->getLocale()),
                    $isGlobalArea ? 'localized_global_area_added' : 'localized_stack_added'
                ));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function add_folder()
    {
        if (!$this->token->validate('add_folder')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $folderName = $this->request->request->get('folderName');
        if (!$this->app->make('helper/validation/strings')->notempty($folderName)) {
            $this->error->add(t('You must give the folder a name.'));
        }

        $parentFolder = null;
        $stackFolderID = $this->request->request->get('stackFolderID');
        if ($this->app->make('helper/validation/numbers')->integer($stackFolderID)) {
            $parentFolder = StackFolder::getByID($stackFolderID);
            if (!is_object($parentFolder)) {
                $this->error->add(t('Unable to find the specified stack folder.'));
            }
        } else {
            $stackFolderID = null;
        }

        if (!$this->error->has()) {
            StackFolder::add($folderName, $parentFolder);
            $parentID = ($stackFolderID === null) ? Page::getByPath(STACKS_PAGE_PATH)->getCollectionID() : $stackFolderID;

            return $this->buildRedirect($this->action('view_details', $parentID, 'folder_added'));
        }

        $this->view();
    }

    public function delete_stack()
    {
        if ($this->token->validate('delete_stack')) {
            $s = Stack::getByID($this->request('stackID'));
            if (is_object($s)) {
                $isGlobalArea = $s->getStackType() == Stack::ST_TYPE_GLOBAL_AREA;
                $neutralStack = $s->getNeutralStack();
                if ($neutralStack === null) {
                    $nextID = $s->getCollectionParentID();
                    if ($isGlobalArea) {
                        $msg = 'global_area_cleared';
                    } else {
                        $msg = 'stack_deleted';
                    }
                } else {
                    $msg = $isGlobalArea ? 'localized_global_area_deleted' : 'localized_stack_deleted';
                    $nextID = $neutralStack->getCollectionID();
                    $section = $s->getMultilingualSection();
                    if ($section) {
                        $nextID .= '@' . $section->getLocale();
                    }
                }

                if (!$this->error->has()) {
                    $sps = new Checker($s);
                    if ($sps->canDeletePage()) {
                        $u = $this->app->make(User::class);
                        $pkr = new DeletePageRequest();
                        $pkr->setRequestedPage($s);
                        $pkr->setRequesterUserID($u->getUserID());
                        $response = $pkr->trigger();
                        if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                            // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                            return $this->buildRedirect($this->action('view_details', rawurlencode($nextID), $msg));
                        }

                        return $this->buildRedirect($this->action('view_details', $s->getCollectionID(), $isGlobalArea ? 'global_area_delete_saved' : 'stack_delete_saved'));
                    }

                    if ($isGlobalArea) {
                        $this->error->add(t('You do not have access to delete this stack.'));
                    } else {
                        $this->error->add(t('You do not have access to delete this global area.'));
                    }
                }
            } else {
                $this->error->add(t('Invalid stack'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function approve_stack($stackID = false, $token = false)
    {
        if ($this->token->validate('approve_stack', $token)) {
            $s = Stack::getByID($stackID);
            if (is_object($s)) {
                $isGlobalArea = $s->getStackType() == Stack::ST_TYPE_GLOBAL_AREA;
                $sps = new Checker($s);
                if ($sps->canApprovePageVersions()) {
                    $u = $this->app->make(User::class);
                    $v = Version::get($s, 'RECENT');
                    $pkr = new ApproveStackRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        return $this->buildRedirect($this->action('view_details', $stackID, $isGlobalArea ? 'global_area_approved' : 'stack_approved'));
                    }

                    return $this->buildRedirect($this->action('view_details', $stackID, 'approve_saved'));
                }

                $this->error->add(t('You do not have access to approve this stack.'));
            } else {
                $this->error->add(t('Invalid stack'));
            }
        } else {
            $this->error->add($this->token->getErrorMessage());
        }
    }

    public function rename($cID)
    {
        $page = null;
        $stack = Stack::getByID($cID);
        if ($stack) {
            $neutralStack = $stack->getNeutralStack();
            if ($neutralStack !== null) {
                return $this->buildRedirect($this->action('rename', $neutralStack->getCollectionID()));
            }

            if ($stack->getStackType() == Stack::ST_TYPE_GLOBAL_AREA) {
                $this->error->add(t("You can't rename global areas"));

                return $this->view_details($cID);
            }

            $isFolder = false;
            $page = $stack;
            $viewCID = $cID;
        } else {
            $folder = StackFolder::getByID($cID);
            if ($folder) {
                $isFolder = true;
                $page = $folder->getPage();
                $viewCID = $page->getCollectionParentID();
            }
        }

        if ($page === null) {
            $this->error->add(t('Invalid stack'));
            $this->view();
        } else {
            $sps = new Checker($page);
            if (!$sps->canEditPageProperties()) {
                if ($isFolder) {
                    $this->error->add(t("You don't have the permission to rename this stack"));
                } else {
                    $this->error->add(t("You don't have the permission to rename this stack folder"));
                }

                $this->view_details($viewCID);
            } else {
                $this->set('renamePage', $page);
                $this->set('isFolder', $isFolder);
                $this->set('oldName', $isFolder ? $page->getCollectionName() : $stack->getStackName());

                if ($this->request->isMethod(Request::METHOD_POST)) {
                    if (!$this->token->validate('rename_stack')) {
                        $this->error->add($this->token->getErrorMessage());
                    } else {
                        $newName = $this->request->request->get('newName');
                        if (!$this->app->make('helper/validation/strings')->notempty($newName)) {
                            $this->error->add(t('The name cannot be empty.'));
                        } else {
                            $txt = $this->app->make('helper/text');
                            $v = $page->getVersionToModify();
                            $v->update([
                                'cName' => $newName,
                                'cHandle' => str_replace('-', $this->app['config']->get('concrete.seo.page_path_separator'), $txt->urlify($newName)),
                            ]);
                            $u = $this->app->make(User::class);
                            if ($isFolder) {
                                $pkr = new ApprovePageRequest();
                            } else {
                                $pkr = new ApproveStackRequest();
                            }

                            $pkr->setRequestedPage($page);
                            $pkr->setRequestedVersionID($v->getVersionID());
                            $pkr->setRequesterUserID($u->getUserID());
                            $response = $pkr->trigger();
                            if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                                // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                                if ($isFolder) {
                                    return $this->buildRedirect($this->action('view_details', $viewCID, 'folder_renamed'));
                                }

                                return $this->buildRedirect($this->action('view_details', $viewCID, 'stack_renamed'));
                            }

                            return $this->buildRedirect($this->action('view_details', $viewCID, 'rename_saved'));
                        }
                    }
                }
            }
        }
    }

    public function move_to_folder()
    {
        if (!$this->token->validate('move_to_folder')) {
            throw new Exception($this->token->getErrorMessage());
        }

        $receivedSourceIDs = $this->request->request->get('sourceIDs');
        if (!is_array($receivedSourceIDs)) {
            throw new Exception(t('Bad parameter: %s', 'sourceIDs'));
        }

        $sourceIDs = [];
        $valn = $this->app->make('helper/validation/numbers');
        foreach ($receivedSourceIDs as $receivedSourceID) {
            if (!$valn->integer($receivedSourceID)) {
                throw new Exception(t('Bad parameter: %s', 'sourceIDs'));
            }
            $receivedSourceID = (int) $receivedSourceID;
            if (!in_array($receivedSourceID, $sourceIDs, true)) {
                $sourceIDs[] = $receivedSourceID;
            }
        }

        if (empty($sourceIDs)) {
            throw new Exception(t('Bad parameter: %s', 'sourceIDs'));
        }

        $moveStacks = [];
        $moveFolders = [];
        $checkedParents = [];
        foreach ($sourceIDs as $sourceID) {
            $parentID = null;
            $item = Stack::getByID($sourceID);
            if ($item) {
                $parentID = $item->getCollectionParentID();
                $moveStacks[] = $item;
            } else {
                $item = StackFolder::getByID($sourceID);
                if (is_object($item)) {
                    $parentID = $item->getPage()->getCollectionParentID();
                    $moveFolders[] = $item;
                } else {
                    throw new Exception(t('Unable to find the specified stack or folder'));
                }
            }

            if ($parentID && !isset($checkedParents[$parentID])) {
                $parent = Page::getByID($parentID);
                if ($parent && !$parent->isError() && !$this->canMoveStacks($parent)) {
                    throw new Exception(t('Access denied'));
                }

                $checkedParents[$parentID] = true;
            }
        }

        $destinationID = $this->request->request->get('destinationID');
        if ($destinationID === '') {
            $destinationPage = Page::getByPath(STACKS_PAGE_PATH);
        } else {
            if (!$valn->integer($destinationID)) {
                throw new Exception(t('Bad parameter: %s', 'destinationID'));
            }

            $destinationFolder = StackFolder::getByID($destinationID);
            if (!is_object($destinationFolder)) {
                throw new Exception(t('Unable to find the specified stack folder'));
            }

            $destinationPage = $destinationFolder->getPage();
        }

        foreach ($moveStacks as $moveStack) {
            $moveStack->move($destinationPage);
        }

        foreach ($moveFolders as $moveFolder) {
            $moveFolder->getPage()->move($destinationPage);
        }

        return JsonResponse::create(
            t2('%d item has been moved under the folder %s', '%d items have been moved under the folder %s', count($sourceIDs), count($sourceIDs), h($destinationPage->getCollectionName()))
        );
    }

    public function duplicate($cID)
    {
        $s = Stack::getByID($cID);
        if (!$s) {
            $this->error->add(t('Invalid stack'));

            return $this->view();
        }

        if ($s->getStackType() == Stack::ST_TYPE_GLOBAL_AREA) {
            $this->error->add(t("You can't duplicate global areas"));
            $this->view_details($cID);
        } else {
            $ns = $s->getNeutralStack();
            if ($ns !== null) {
                return $this->buildRedirect($this->action('duplicate', $ns->getCollectionID()));
            }

            $sps = new Checker($s);
            if (!$sps->canMoveOrCopyPage()) {
                $this->error->add(t("You don't have the permission to clone this stack"));
                $this->view_details($cID);
            } else {
                $this->set('duplicateStack', $s);
                if ($this->request->isMethod(Request::METHOD_POST)) {
                    if (!$this->token->validate('duplicate_stack')) {
                        $this->error->add($this->token->getErrorMessage());
                    } else {
                        $stackName = $this->request->request->get('stackName');
                        if (!$this->app->make('helper/validation/strings')->notempty($stackName)) {
                            $this->error->add(t('You must give your stack a name.'));
                        } else {
                            $ns = $s->duplicate();
                            $ns->update([
                                'stackName' => $stackName,
                            ]);

                            $ns->copyLocalizedStacksFrom($s);

                            return $this->buildRedirect($this->action('view_details', $s->getCollectionParentID(), 'stack_duplicated'));
                        }
                    }
                }
            }
        }
    }

    public function list_page()
    {
        return Redirect::to('/');
    }

    public function delete_stackfolder()
    {
        $parentID = null;
        if (!$this->token->validate('delete_stackfolder')) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $folder = StackFolder::getByID($this->request->request->get('stackfolderID'));
            if (!is_object($folder)) {
                $this->error->add(t('Unable to find the specified stack folder.'));
            } else {
                $parentID = $folder->getPage()->getCollectionParentID();
                $sps = new Checker($folder->getPage());
                if (!$sps->canDeletePage()) {
                    $this->error->add(t('You do not have access to delete this stack folder.'));
                } else {
                    $u = $this->app->make(User::class);
                    $pkr = new DeletePageRequest();
                    $pkr->setRequestedPage($folder->getPage());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        return $this->buildRedirect($this->action('view_details', $parentID, 'folder_deleted'));
                    }

                    return $this->buildRedirect($this->action('view_details', $parentID, 'folder_delete_saved'));
                }
            }
        }

        if ($parentID) {
            $this->view_details($parentID);
        } else {
            $this->view();
        }
    }

    public function usage($stackId)
    {
        $this->set('stackId', $stackId);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app->make(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(StackUsageRecord::class);

        $records = $repository->findBy([
            'stack_id' => $stackId,
        ]);

        $view = new \Concrete\Core\View\DialogView('dialogs/stack/usage');
        $view->setController($this);
        $view->addScopeItems([
            'records' => $this->getUsageGenerator($records),
        ]);

        return new Response($view->render());
    }

    public function stack_duplicated()
    {
        $this->set('message', t('Stack duplicated successfully'));
        $this->view();
    }

    protected function createBreadcrumbFactory(): DashboardStacksBreadcrumbFactory
    {
        return $this->app->make(DashboardStacksBreadcrumbFactory::class);
    }

    /**
     * @return Section[]
     */
    protected function getMultilingualSections()
    {
        $result = [];
        if ($this->app->make('multilingual/detector')->isEnabled()) {
            foreach (Section::getList() as $section) {
                // @var Section $section
                $result[$section->getLocale()] = $section;
            }
            uasort($result, function (Section $a, Section $b) {
                $r = strcasecmp($a->getLanguageText(), $b->getLanguageText());
                if ($r === 0) {
                    $r = strcasecmp($a->getLocale(), $b->getLocale());
                }

                return $r;
            });
        }

        return $result;
    }

    /**
     * Check if stacks in a Page or StackFolder can be moved.
     *
     * @param Page|StackFolder $parent
     *
     * @return bool
     */
    protected function canMoveStacks($parent)
    {
        $page = ($parent instanceof \Concrete\Core\Page\Page) ? $parent : $parent->getPage();
        $cpc = new Checker($page);

        return (bool) $cpc->canMoveOrCopyPage();
    }

    protected function deliverStackList(StackList $list)
    {
        $list->setFoldersFirst(true);
        // $list->setSiteTreeObject($this->getSite()->getSiteTreeObject());
        $this->set('list', $list);
        $this->set('stacks', $list->getResults());
    }

    /**
     * Generator for transforming a list of StackUsageRecords into Collection objects
     * This method can be used to do some interesting things with the list two sine it is ordered
     * by Collection ID /and/ Collection Version ID.
     *
     * @param StackUsageRecord[] $records
     *
     * @return \Generator
     */
    protected function getUsageGenerator(array $records)
    {
        $last_collection = null;

        foreach ($records as $record) {
            if ($last_collection && $last_collection->getCollectionID() == $record->getCollectionId()) {
                // This is the same collection as the last collection, lets use it again.
                $collection = $last_collection;
            } else {
                /** @var \Concrete\Core\Page\Collection\Collection $collection */
                $last_collection = $collection = Page::getByID($record->getCollectionId());
            }

            // Load in the version object
            $collection->loadVersionObject($record->getCollectionVersionId());

            // Yield the collection object
            yield $collection;
        }
    }
}
