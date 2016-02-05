<?php
namespace Concrete\Controller\SinglePage\Dashboard\Blocks;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Stack\StackCategory;
use Concrete\Core\Support\Facade\StackFolder;
use Config;
use Concrete\Core\Page\Stack\StackList;
use Stack;
use Page;
use Permissions;
use User;
use Concrete\Core\Workflow\Request\DeletePageRequest;
use Concrete\Core\Workflow\Request\ApproveStackRequest;
use View;
use Exception;
use Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;

class Stacks extends DashboardPageController
{
    public function view_global_areas()
    {
        $stm = new StackList();
        $stm->filterByGlobalAreas();
        $this->deliverStackList($stm);
        $this->set('breadcrumb', $this->getBreadcrumb());
    }

    /**
     * @return Section[]
     */
    protected function getMultilingualSections()
    {
        $result = array();
        if ($this->app->make('multilingual/detector')->isEnabled()) {
            foreach (Section::getList() as $section) {
                /* @var Section $section */
                $result[$section->getLocale()] = $section;
            }
            uasort($result, function (Section $a, Section $b) {
                $r = strcasecmp($a->getLanguageText(), $a->getLanguageText());
                if ($r === 0) {
                    $r = strcasecmp($a->getLocale(), $b->getLocale());
                }
                return $r;
            });
        }

        return $result;
    }

    public function view_details($cID, $msg = false)
    {
        if (strpos($cID, '@') !== false) {
            list($cID, $locale) = explode('@', $cID, 2);
        } else {
            $locale = '';
        }
        $s = Stack::getByID($cID);
        if (is_object($s)) {
            $isGlobalArea = $s->getStackType() == Stack::ST_TYPE_GLOBAL_AREA;
            if ($s->isNeutralStack()) {
                $neutralStack = $s;
                $stackToEdit = $s;
            } else {
                $neutralStack = $s->getNeutralStack();
                $stackToEdit = $s;
            }
            $sections = $this->getMultilingualSections();
            $breadcrumb = $this->getBreadcrumb($neutralStack);
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
                $localeCrumbs = array();
                $localeCrumbs[] = array(
                    'id' => $neutralStack->getCollectionID(),
                    'active' => $locale === '',
                    'name' => h(tc('Locale', 'default')),
                    'url' => \URL::to('/dashboard/blocks/stacks', 'view_details', $neutralStack->getCollectionID()),
                );
                $mif = $this->app->make('multilingual/interface/flag');
                /* @var \Concrete\Core\Multilingual\Service\UserInterface\Flag $mif */
                foreach ($sections as $sectionLocale => $section) {
                    /* @var Section $section */
                    $localeCrumbs[] = array(
                        'id' => $neutralStack->getCollectionID().'@'.$sectionLocale,
                        'active' => $locale === $sectionLocale,
                        'name' => $mif->getSectionFlagIcon($section).' '.h($section->getLanguageText()).' <span class="text-muted">'.h($sectionLocale).'</span>',
                        'url' => \URL::to('/dashboard/blocks/stacks', 'view_details', $neutralStack->getCollectionID().rawurlencode('@'.$sectionLocale)),
                    );
                }
                foreach ($localeCrumbs as $localeCrumb) {
                    if ($localeCrumb['active']) {
                        $localeCrumb['children'] = array_filter($localeCrumbs, function ($child) {
                           return !$child['active'];
                        });
                        $breadcrumb[] = $localeCrumb;
                        break;
                    }
                }
            }
            if ($stackToEdit !== null) {
                $blocks = $stackToEdit->getBlocks('Main');
                $view = View::getInstance();
                foreach ($blocks as $b1) {
                    $btc = $b1->getInstance();
                    // now we inject any custom template CSS and JavaScript into the header
                    if ($btc instanceof \Concrete\Core\Block\BlockController) {
                        $btc->outputAutoHeaderItems();
                    }
                    $btc->runTask('on_page_view', array($view));
                }
                $this->addHeaderItem($stackToEdit->outputCustomStyleHeaderItems(true));
                $this->set('blocks', $blocks);
            }
            $this->set('neutralStack', $neutralStack);
            $this->set('stackToEdit', $stackToEdit);
            $this->set('breadcrumb', $breadcrumb);
            $this->set('isGlobalArea', $isGlobalArea);
            switch ($msg) {
                case 'stack_added':
                    $this->set('message', t('Stack added successfully.'));
                    break;
                case 'localized_stack_added':
                    $this->set('message', t('Localized version of stack added successfully.'));
                    break;
                case 'stack_approved':
                    $this->set('message', t('Stack approved successfully'));
                    break;
                case 'approve_saved':
                    $this->set('message', t('Approve request saved. You must complete the approval workflow before these changes are publicly accessible.'));
                    break;
                case 'delete_saved':
                    $this->set('message', t('Delete request saved. You must complete the delete workflow before this stack can be deleted.'));
                    break;
                case 'rename_saved':
                    $this->set('message', t('Rename request saved. You must complete the approval workflow before the name of the stack will be updated.'));
                    break;
                case 'stack_deleted':
                    $this->set('message', t('Stack deleted successfully'));
                    break;
                case 'localized_stack_deleted':
                    $this->set('message', t('Localized version of stack deleted successfully'));
                    break;
            }
        } else {
            $folder = StackFolder::getByID($cID);
            if (is_object($folder)) {
                $stm = new StackList();
                $stm->filterByFolder($folder);
                $this->set('currentStackFolderID', $folder->getPage()->getCollectionID());
                $this->set('breadcrumb', $this->getBreadcrumb($folder->getPage()));
                $this->set('current', $current);
                $this->deliverStackList($stm);
                $this->set('canMoveStacks', $this->canMoveStacks($folder));
            } else {
                $root = Page::getByPath(STACKS_PAGE_PATH);
                if ($root->getCollectionID() != $cID) {
                    $this->error->add(t('Invalid stack'));
                }
                $this->view();
            }
        }
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
        $cpc = new Permissions($page);
        return (bool) $cpc->canMoveOrCopyPage();
    }

    protected function getBreadcrumb(\Concrete\Core\Page\Page $page = null)
    {
        $breadcrumb = [[
            'active' => false,
            'name' => t('Stacks & Global Areas'),
            'url' => \URL::to('/dashboard/blocks/stacks'),
        ]];
        $nav = $this->app->make('helper/navigation');
        if ($page === null) {
            $breadcrumb[] = [
                'id' => '',
                'active' => true,
                'name' => t('Global Areas'),
                'url' => \URL::to('/dashboard/blocks/stacks', 'view_global_areas'),
            ];
        } else {
            $pages = array_reverse($nav->getTrailToCollection($page));
            $pages[] = $page;
            for ($i = 1; $i < count($pages); ++$i) {
                $item = $pages[$i];
                $breadcrumb[] = [
                    'id' => $item->getCollectionID(),
                    'active' => $item->getCollectionID() == $page->getCollectionID(),
                    'name' => $item->getCollectionName(),
                    'url' => \URL::to('/dashboard/blocks/stacks', 'view_details', $item->getCollectionID()),
                ];
            }
        }

        return $breadcrumb;
    }

    protected function deliverStackList(StackList $list)
    {
        $list->setFoldersFirst(true);
        $this->set('list', $list);
        $this->set('stacks', $list->getResults());
    }

    public function view()
    {
        $parent = Page::getByPath(STACKS_PAGE_PATH);
        $stm = new StackList();
        $stm->excludeGlobalAreas();
        $stm->filterByParentID($parent->getCollectionID());
        $this->deliverStackList($stm);
        $this->set('canMoveStacks', $this->canMoveStacks($parent));
        $this->set('showGlobalAreasFolder', true);
    }

    public function add_stack()
    {
        if ($this->app->make('helper/validation/token')->validate('add_stack')) {
            $stackName = $this->post('stackName');
            if ($this->app->make('helper/validation/strings')->notempty($stackName)) {
                $folder = null;
                $folderID = $this->post('stackFolderID');
                if ($this->app->make('helper/validation/numbers')->integer($folderID)) {
                    $folder = StackFolder::getByID($folderID);
                    if (!is_object($folder)) {
                        $this->error->add(t("Unable to find the specified stack folder."));
                    }
                }
                if (!$this->error->has()) {
                    $stack = Stack::addStack($stackName, $folder);
                    $this->redirect('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID(), 'stack_added');
                }
            } else {
                $this->error->add(t("You must give your stack a name."));
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
    }

    public function add_localized_stack()
    {
        $token = $this->app->make('helper/validation/token');
        /* @var \Concrete\Core\Validation\CSRF\Token $token */
        if ($token->validate('add_localized_stack')) {
            $neutralStack = Stack::getByID($this->post('stackID'));
            if (!$neutralStack) {
                $this->error->add(t('Unable to find the specified stack'));
            }
            $section = Section::getByLocale($this->post('locale'));
            if (!$section) {
                $this->error->add(t('Unable to find the specified language'));
            }
            if (!$this->error->has()) {
                $localizedStack = $neutralStack->addLocalizedStack($section);
                $this->redirect('/dashboard/blocks/stacks', 'view_details', $neutralStack->getCollectionID().rawurlencode('@'.$section->getLocale()), 'localized_stack_added');
            } else {
                $this->error->add(t("You must give your stack a name."));
            }
        } else {
            $this->error->add($token->getErrorMessage());
        }
    }

    public function add_folder()
    {
        if (!$this->token->validate('add_folder')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $folderName = $this->post('folderName');
        if (!$this->app->make('helper/validation/strings')->notempty($folderName)) {
            $this->error->add(t("You must give the folder a name."));
        }
        $parentFolder = null;
        $stackFolderID = $this->post('stackFolderID');
        if ($this->app->make('helper/validation/numbers')->integer($stackFolderID)) {
            $parentFolder = StackFolder::getByID($this->post('stackFolderID'));
            if (!is_object($parentFolder)) {
                $this->error->add(t("Unable to find the specified stack folder."));
            }
        } else {
            $stackFolderID = null;
        }
        if (!$this->error->has()) {
            StackFolder::add($folderName, $parentFolder);
            $this->flash('success', t('Folder added successfully.'));
            if ($stackFolderID === null) {
                $this->redirect('/dashboard/blocks/stacks');
            } else {
                $this->redirect('/dashboard/blocks/stacks', 'view_details', $stackFolderID);
            }
        }
        $this->view();
    }

    public function delete_stack()
    {
        if ($this->app->make('helper/validation/token')->validate('delete_stack')) {
            $s = Stack::getByID($_REQUEST['stackID']);
            if (is_object($s)) {
                $neutralStack = $s->getNeutralStack();
                $locale = '';
                if ($neutralStack === null) {
                    $nextID = $s->getCollectionParentID();
                    $msg = 'stack_deleted';
                } else {
                    $nextID = $neutralStack->getCollectionID();
                    $msg = 'localized_stack_deleted';
                    $section = $s->getMultilingualSection();
                    if ($section) {
                        $locale = $section->getLocale();
                    }
                }
                $sps = new Permissions($s);
                if ($sps->canDeletePage()) {
                    $u = new User();
                    $pkr = new DeletePageRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $nextID.rawurlencode('@'.$locale), $msg);
                    } else {
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $s->cID, 'delete_saved');
                    }
                } else {
                    $this->error->add(t('You do not have access to delete this stack.'));
                }
            } else {
                $this->error->add(t('Invalid stack'));
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
    }

    public function approve_stack($stackID = false, $token = false)
    {
        if ($this->app->make('helper/validation/token')->validate('approve_stack', $token)) {
            $s = Stack::getByID($stackID);
            if (is_object($s)) {
                $sps = new Permissions($s);
                if ($sps->canApprovePageVersions()) {
                    $u = new User();
                    $v = Version::get($s, 'RECENT');
                    $pkr = new ApproveStackRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $stackID, 'stack_approved');
                    } else {
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $stackID, 'approve_saved');
                    }
                } else {
                    $this->error->add(t('You do not have access to approve this stack.'));
                }
            } else {
                $this->error->add(t('Invalid stack'));
            }
        } else {
            $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
        }
    }

    public function rename($cID)
    {
        $s = Stack::getByID($cID);
        if (is_object($s)) {
            $this->set('stack', $s);
        } else {
            throw new Exception(t('Invalid stack'));
        }
        $sps = new Permissions($s);
        if (!$sps->canEditPageProperties()) {
            $this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
        }

        if ($this->isPost()) {
            if ($this->app->make('helper/validation/token')->validate('rename_stack')) {
                if ($this->app->make('helper/validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
                    $txt = $this->app->make('helper/text');
                    $v = $s->getVersionToModify();
                    $v->update(array(
                        'cName' => $stackName,
                        'cHandle' => str_replace('-', Config::get('concrete.seo.page_path_separator'), $txt->urlify($stackName)),
                    ));

                    $u = new User();
                    $pkr = new ApproveStackRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequestedVersionID($v->getVersionID());
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        $this->redirect('/dashboard/blocks/stacks', 'stack_renamed', $cID);
                    } else {
                        $this->redirect('/dashboard/blocks/stacks', 'view_details', $cID, 'rename_saved');
                    }
                } else {
                    $this->error->add(t("The stack name cannot be empty."));
                }
            } else {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
        }
    }

    public function stack_renamed($cID)
    {
        $this->set('message', t('Stack renamed successfully'));
        $this->view_details($cID);
        $this->action = 'view_details';
    }

    public function move_to_folder()
    {
        $valt = $this->app->make('helper/validation/token');
        if (!$valt->validate('move_to_folder')) {
            throw new Exception($valt->getErrorMessage());
        }
        $valn = $this->app->make('helper/validation/numbers');
        /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
        $receivedSourceIDs = $this->post('sourceIDs');
        if (!is_array($receivedSourceIDs)) {
            throw new Exception(t("Bad parameter: %s", 'sourceIDs'));
        }
        $sourceIDs = array();
        foreach ($receivedSourceIDs as $receivedSourceID) {
            if (!$valn->integer($receivedSourceID)) {
                throw new Exception(t("Bad parameter: %s", 'sourceIDs'));
            }
            $receivedSourceID = (int) $receivedSourceID;
            if (!in_array($receivedSourceID, $sourceIDs, true)) {
                $sourceIDs[] = $receivedSourceID;
            }
        }
        if (empty($sourceIDs)) {
            throw new Exception(t("Bad parameter: %s", 'sourceIDs'));
        }
        $moveStacks = array();
        $moveFolders = array();
        $checkedParents = array();
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
                    throw new Exception(t("Unable to find the specified stack or folder"));
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
        $destinationID = $this->post('destinationID');
        if ($destinationID === '') {
            $destinationPage = Page::getByPath(STACKS_PAGE_PATH);
        } else {
            if (!$valn->integer($destinationID)) {
                throw new Exception(t("Bad parameter: %s", 'destinationID'));
            }
            $destinationFolder = StackFolder::getByID($destinationID);
            if (!is_object($destinationFolder)) {
                throw new Exception(t("Unable to find the specified stack folder"));
            }
            $destinationPage = $destinationFolder->getPage();
        }
        foreach ($moveStacks as $moveStack) {
            $moveStack->move($destinationPage);
        }
        foreach ($moveFolders as $moveFolder) {
            $moveFolder->getPage()->move($destinationPage);
        }
        JsonResponse::create(
            t2('%d item has been moved under the folder %s!', '%d items have been moved under the folder %s!', count($sourceIDs), count($sourceIDs), h($destinationPage->getCollectionName()))
        )->send();
        exit;
    }

    public function duplicate($cID)
    {
        $s = Stack::getByID($cID);
        if (is_object($s)) {
            $this->set('stack', $s);
        } else {
            throw new Exception(t('Invalid stack'));
        }
        $sps = new Permissions($s);
        if (!$sps->canMoveOrCopyPage()) {
            $this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
        }

        if ($this->isPost()) {
            if ($this->app->make('helper/validation/token')->validate('duplicate_stack')) {
                if ($this->app->make('helper/validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
                    $ns = $s->duplicate();
                    $ns->update(array(
                        'stackName' => $stackName,
                    ));

                    $this->redirect('/dashboard/blocks/stacks', 'stack_duplicated');
                } else {
                    $this->error->add(t("You must give your stack a name."));
                }
            } else {
                $this->error->add($this->app->make('helper/validation/token')->getErrorMessage());
            }
            $name = trim($this->post('name'));
        }
    }

    public function stack_duplicated()
    {
        $this->set('message', t('Stack duplicated successfully'));
        $this->view();
    }

    public function update_order()
    {
        $ret = array('success' => false, 'message' => t("Error"));
        if ($this->isPost() && is_array($stIDs = $this->post('stID'))) {
            $parent = Page::getByPath(STACKS_PAGE_PATH);
            $cpc = new Permissions($parent);
            if ($cpc->canMoveOrCopyPage()) {
                foreach ($stIDs as $displayOrder => $cID) {
                    $c = Page::getByID($cID);
                    $c->updateDisplayOrder($displayOrder, $cID);
                }
                $ret['success'] = true;
                $ret['message'] = t("Stack order updated successfully.");
            }
        }
        echo $this->app->make('helper/json')->encode($ret);
        exit;
    }

    public function list_page()
    {
        return Redirect::to('/');
    }
}
