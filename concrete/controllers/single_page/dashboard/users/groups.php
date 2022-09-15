<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardGroupBreadcrumbFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\Group\FolderManager;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\Search\Field\Field\FolderField;
use Concrete\Core\User\Group\Search\Menu\MenuFactory;
use Concrete\Core\User\Group\Search\SearchProvider;
use Exception;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\Request;

class Groups extends DashboardPageController
{
    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('groups/search/menu');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('groups/search/search');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        $headerMenu->getElementController()->setQuery($result->getQuery());
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        /** @var \Concrete\Core\Entity\Search\Query $query */
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedGroupSearch::class, $presetID);

            if ($preset) {
                /** @noinspection PhpParamsInspection */
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                return;
            }
        }

        $this->view();
    }

    /**
     * @return DashboardGroupBreadcrumbFactory
     */
    protected function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardGroupBreadcrumbFactory::class);
    }

    public function view()
    {
        $rootFolder = $this->getRootFolder();

        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
        $this->setCurrentFolder($rootFolder);

        $this->headerSearch->getElementController()->setQuery(null);
    }

    public function view_tree()
    {
        $rootFolder = $this->getRootFolder();
        $this->set('headerMenu', $this->app->make(ElementManager::class)->get('dashboard/groups/menu_tree'));
        $this->set('tree', $rootFolder->getTreeObject());
        $this->render('/dashboard/users/groups/tree');
    }


    protected function getRootFolder()
    {
        /** @var FolderManager $folderManager */
        $folderManager = $this->app->make(FolderManager::class);
        $rootFolder = $folderManager->getRootFolder();
        return $rootFolder;
    }

    /**
     * Responsible for setting the current folder in the header menu, and in the JavaScript that powers the search table.
     *
     * @param GroupFolder|Node $folder
     */
    protected function setCurrentFolder(Node $folder)
    {
        $this->set('folderID', $folder->getTreeNodeID());
        $this->headerMenu->getElementController()->setCurrentFolder($folder);
    }

    /**
     * @param GroupFolder|Node $folder
     * @return FolderField
     */
    protected function getSearchFolderField(Node $folder)
    {
        // This method is called in two spots: 1. the basic search, 2. when you browser to a sub folder.
        // In both cases, if keywords are present to further filter the search, we want to not only search
        // the folder you're specifying, but ALSO the sub-folders.
        // Note, this is separate from any folders specified within advanced search.
        if ($this->request->query->has('keywords')) {
            $field = new FolderField($folder, true);
        } else {
            $field = new FolderField($folder);
        }
        return $field;
    }

    public function folder($folderID = null)
    {
        if ($folderID) {
            $folder = Node::getByID($folderID);
            if ($folder && $folder instanceof \Concrete\Core\Tree\Node\Type\Group) {
                $group = $folder->getTreeNodeGroupObject();
                $countOfChildGroups = count($group->getChildGroups());
                if ($countOfChildGroups > 0) {
                    // Add support for legacy groups that are contains sub groups
                    // Let's treat them like a regular folder
                    $query = $this->getQueryFactory()->createQuery(
                        $this->getSearchProvider(), [
                            $this->getSearchKeywordsField(),
                            $this->getSearchFolderField($folder),
                        ]
                    );
                    $result = $this->createSearchResult($query);
                    $this->renderSearchResult($result);

                    $factory = $this->createBreadcrumbFactory();
                    $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $folder));
                    $this->headerSearch->getElementController()->setHeaderSearchAction(
                        $this->app->make('url')->to('/dashboard/users/groups', 'folder', $folder->getTreeNodeID())
                    );
                    $this->setCurrentFolder($folder);
                    return;
                }
            }
            if ($folder && $folder instanceof GroupFolder) {
                $query = $this->getQueryFactory()->createQuery(
                    $this->getSearchProvider(), [
                        $this->getSearchKeywordsField(),
                        $this->getSearchFolderField($folder),
                    ]
                );
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $folder));
                $this->headerSearch->getElementController()->setHeaderSearchAction(
                    $this->app->make('url')->to('/dashboard/users/groups', 'folder', $folder->getTreeNodeID())
                );
                $this->setCurrentFolder($folder);
                return;
            }
        }
        $this->view();
    }

    public function edit($gID = false)
    {
        $g = Group::getByID(intval($gID));
        $gp = new Checker($g);
        if (!is_object($g)) {
            throw new \Exception(t('Invalid group.'));
        }
        if (!$gp->canEditGroup()) {
            throw new \Exception(t('You do not have access to edit this group.'));
        }
        if (is_object($g)) {
            $this->set('group', $g);
        }
        $this->render("/dashboard/users/groups/edit");
    }

    public function bulk_update_complete()
    {
        $this->set('success', t('Groups moved successfully.'));
        $this->view();
    }

    public function update_group()
    {
        $cntp = Page::getByPath('/dashboard/users/add_group');
        /** @var \Concrete\Controller\SinglePage\Dashboard\Users\AddGroup $cnta */
        $cnta = $cntp->getController();

        $g = Group::getByID(intval($_REQUEST['gID']));
        if (is_object($g)) {
            $this->set('group', $g);
        }
        $gp = new Checker($g);
        if (!$gp->canEditGroup()) {
            $this->error->add(t('You do not have access to edit this group.'));
        }

        $txt = $this->app->make('helper/text');
        $valt = $this->app->make('helper/validation/token');
        $gName = $txt->sanitize($_POST['gName']);
        $gDescription = $_POST['gDescription'];

        if (!$gName) {
            $this->error->add(t("Name required."));
        }

        if (!$valt->validate('add_or_update_group')) {
            $this->error->add($valt->getErrorMessage());
        }

        foreach($cnta->validateRoles()->getList() as $error) {
            $this->error->add($error);
        }

        if (!$this->error->has()) {
            $g->update($gName, $_POST['gDescription']);
            $cnta->checkExpirationOptions($g);
            $cnta->checkBadgeOptions($g);
            $cnta->checkAutomationOptions($g);
            $cnta->checkGroupTypeOptions($g);
            $this->redirect('/dashboard/users/groups', 'group_updated');
        } else {
            $this->render("/dashboard/users/groups/edit");
        }
    }

    public function group_added()
    {
        $this->set('message', t('Group added successfully'));
        $this->view();
    }

    public function group_updated()
    {
        $this->set('message', t('Group update successfully'));
        $this->view();
    }

    public function delete($delGroupId, $token = '')
    {
        $u = $this->app->make(User::class);
        try {
            if (!$u->isSuperUser()) {
                throw new Exception(t('You do not have permission to perform this action.'));
            }

            $group = Group::getByID($delGroupId);

            if (!($group instanceof Group)) {
                throw new Exception(t('Invalid group ID.'));
            }

            $valt = $this->app->make('helper/validation/token');
            if (!$valt->validate('delete_group_' . $delGroupId, $token)) {
                throw new Exception($valt->getErrorMessage());
            }

            if ($group->delete() === false) {
                throw new Exception(t("This group can't be deleted"));
            }
            $resultMsg = t('Group deleted successfully.');
            $this->set('message', $resultMsg);
        } catch (Exception $e) {
            $this->error->add($e);
        }
        $this->view();
    }
}
