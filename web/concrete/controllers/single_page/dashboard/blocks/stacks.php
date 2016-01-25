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
use Loader;
use User;
use Concrete\Core\Workflow\Request\DeletePageRequest;
use Concrete\Core\Workflow\Request\ApproveStackRequest;
use View;
use Exception;
use Redirect;
use Core;

class Stacks extends DashboardPageController
{
    /*
    protected $multilingualSections = array();

    public function on_start()
    {
        parent::on_start();
        if (\Core::make('multilingual/detector')->isEnabled()) {
            $list = array();
            $this->multilingualSections = Section::getList();
            $this->set('multilingualSections', $this->multilingualSections);
            $this->set('defaultLanguage', $this->getSelectedLanguage());
        }
    }

    protected function getSelectedLanguage()
    {
        $defaultLanguage = false;
        $defaultLocale = Config::get('concrete.multilingual.default_locale');
        if (!$defaultLocale) {
            throw new \Exception(t('You must specify a default language tree in your multilingual settings.'));
        }
        foreach ($this->multilingualSections as $section) {
            if ($section->getLocale() == $defaultLocale) {
                $defaultLanguage = $section;
            }
        }
        $session = Core::make('session');
        if ($session->has('stacksDefaultLanguageID')) {
            $section = Section::getByID($session->get('stacksDefaultLanguageID'));
            if (is_object($section)) {
                $defaultLanguage = $section;
            }
        }

        return $defaultLanguage;
    }

    public function set_default_language($language = null, $action = null)
    {
        $session = Core::make('session');
        $session->set('stacksDefaultLanguageID', $language);
        if ($action == 'view_global_areas') {
            $this->redirect('/dashboard/blocks/stacks', 'view_global_areas');
        } else {
            $this->redirect('/dashboard/blocks/stacks');
        }
    }

    public function view_global_areas()
    {
        $stm = new StackList();
        $stm->filterByGlobalAreas();
        $this->setupMultilingualStackList($stm);
        $this->set('stacks', $stm->get());
    }

    protected function setupMultilingualStackList(\Concrete\Core\Page\Stack\StackList $list)
    {
        if (\Core::make('multilingual/detector')->isEnabled()) {
            $category = StackCategory::getCategoryFromMultilingualSection($this->getSelectedLanguage());
            if (!is_object($category)) {
                // we have to create the category
                $category = StackCategory::createFromMultilingualSection($this->getSelectedLanguage());
                // now we copy all the stacks into it
                $default = StackCategory::getFromDefaultMultilingualSection();
                $default->copyToTargetCategory($category);
            }
            $list->filterByStackCategory($category);
        }
    }*/


    public function view_details($cID, $msg = false)
    {
        $s = Stack::getByID($cID);
        if (is_object($s)) {
            $blocks = $s->getBlocks('Main');
            $view = View::getInstance();
            foreach ($blocks as $b1) {
                $btc = $b1->getInstance();
                // now we inject any custom template CSS and JavaScript into the header
                if ($btc instanceof \Concrete\Core\Block\BlockController) {
                    $btc->outputAutoHeaderItems();
                }
                $btc->runTask('on_page_view', array($view));
            }
            $this->addHeaderItem($s->outputCustomStyleHeaderItems(true));

            $this->set('stack', $s);
            $this->set('breadcrumb', $this->getBreadcrumb($s));
            $this->set('blocks', $blocks);
            switch ($msg) {
                case 'stack_added':
                    $this->set('message', t('Stack added successfully.'));
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
            }
        } else {
            $folder = StackFolder::getByID($cID);
            if (is_object($folder)) {
                $stm = new StackList();
                $stm->filterByFolder($folder);
                $this->set('breadcrumb', $this->getBreadcrumb($folder->getPage()));
                $this->set('current', $current);
                $this->deliverStackList($stm);
            } else {
                throw new Exception(t('Invalid stack'));
            }
        }
    }

    protected function getBreadcrumb(\Concrete\Core\Page\Page $page)
    {
        $breadcrumb = array([
            'active' => false,
            'name' => t('Stacks'),
            'url' => \URL::to('/dashboard/blocks/stacks')
        ]);
        $nav = $this->app->make('helper/navigation');
        $pages = $nav->getTrailToCollection($page);
        $pages[] = $page;
        for ($i = 1; $i < count($pages); $i++) {
            $item = $pages[$i];
            $breadcrumb[] = [
                'active' => $item->getCollectionID() == $page->getCollectionID(),
                'name' => $item->getCollectionName(),
                'url' => \URL::to('/dashboard/blocks/stacks', 'view_details', $item->getCollectionID())
            ];
        }
        return $breadcrumb;
    }


    protected function deliverStackList($list)
    {
        $this->set('list', $list);
        $this->set('stacks', $list->getResults());
    }

    public function view()
    {
        $parent = Page::getByPath(STACKS_PAGE_PATH);
        $stm = new StackList();
        $stm->filterByParentID($parent->getCollectionID());
        $this->deliverStackList($stm);
        /*$cpc = new Permissions($parent);
        if ($cpc->canMoveOrCopyPage()) {
            $this->set('canMoveStacks', true);
            $sortUrl = View::url('/dashboard/blocks/stacks', 'update_order');
            $this->set('sortURL', $sortUrl);
        }
        */
    }

    public function add_stack()
    {
        if (Loader::helper('validation/token')->validate('add_stack')) {
            if (Loader::helper('validation/strings')->notempty($this->post('stackName'))) {
                $stack = Stack::addStack($this->post('stackName'));
                $this->redirect('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID(), 'stack_added');
            } else {
                $this->error->add(t("You must give your stack a name."));
            }
        } else {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
    }

    public function add_folder()
    {
        if (!$this->token->validate('add_folder')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!Loader::helper('validation/strings')->notempty($this->post('folderName'))) {
            $this->error->add(t("You must give the folder a name."));
        }
        if (!$this->error->has()) {
            $stack = StackFolder::add($this->post('folderName'));
            $this->flash('success', t('Folder added successfully.'));
            $this->redirect('/dashboard/blocks/stacks');
        }
        $this->view();
    }


    public function stack_deleted()
    {
        $this->set('message', t('Stack deleted successfully'));
        $this->view();
    }

    public function delete_stack()
    {
        if (Loader::helper('validation/token')->validate('delete_stack')) {
            $s = Stack::getByID($_REQUEST['stackID']);
            if (is_object($s)) {
                $sps = new Permissions($s);
                if ($sps->canDeletePage()) {
                    $u = new User();
                    $pkr = new DeletePageRequest();
                    $pkr->setRequestedPage($s);
                    $pkr->setRequesterUserID($u->getUserID());
                    $response = $pkr->trigger();
                    if ($response instanceof \Concrete\Core\Workflow\Progress\Response) {
                        // we only get this response if we have skipped workflows and jumped straight in to an approve() step.
                        $this->redirect('/dashboard/blocks/stacks', 'stack_deleted');
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
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
    }

    public function approve_stack($stackID = false, $token = false)
    {
        if (Loader::helper('validation/token')->validate('approve_stack', $token)) {
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
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
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
            if (Loader::helper('validation/token')->validate('rename_stack')) {
                if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
                    $txt = Loader::helper('text');
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
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
            }
        }
    }

    public function stack_renamed($cID)
    {
        $this->set('message', t('Stack renamed successfully'));
        $this->view_details($cID);
        $this->task = 'view_details';
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
            if (Loader::helper('validation/token')->validate('duplicate_stack')) {
                if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
                    $ns = $s->duplicate();
                    $ns->update(array(
                        'stackName' => $stackName,
                    ));

                    $this->redirect('/dashboard/blocks/stacks', 'stack_duplicated');
                } else {
                    $this->error->add(t("You must give your stack a name."));
                }
            } else {
                $this->error->add(Loader::helper('validation/token')->getErrorMessage());
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
        echo Loader::helper('json')->encode($ret);
        exit;
    }

    public function list_page()
    {
        return Redirect::to('/');
    }
}
