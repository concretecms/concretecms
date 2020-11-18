<?php /** @noinspection PhpUnused */

namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Checker;
use \Concrete\Core\Permission\Key\Key;
use \Concrete\Core\Permission\Access\PageAccess;
use \Concrete\Core\Permission\Duration;

class Permissions extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/page/bulk/permissions';
    /** @var Page[] */
    protected $pages = null;
    protected $canEdit = false;
    protected $permissionsInherit = '';
    /** @var Repository */
    protected $config;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();

        $this->config = $this->app->make(Repository::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);

        if (!isset($this->pages)) {
            $this->pages = [];
            $permissionsInherit = [];

            if ($this->request->getMethod() === "POST" && !$this->request->query->has('item')) {
                $pageIds = $this->request->request->get('item');
            } else {
                $pageIds = $this->request->query->get('item');
            }

            if (!is_array($pageIds)) {
                $pageIds = [$pageIds];
            }

            foreach ($pageIds as $pageId) {
                $page = Page::getByID($pageId);

                if ($page instanceof Page && !$page->isError()) {
                    $this->pages[] = $page;
                    $permissionsInherit[] = $page->getCollectionInheritance();
                }
            }

            $permissionsInherit = array_unique($permissionsInherit);

            if (count($permissionsInherit) == 1) {
                $this->permissionsInherit = $permissionsInherit[0];
            }

            if (count($this->pages) > 0) {
                $this->canEdit = true;

                foreach ($this->pages as $c) {
                    $cp = new Checker($c);

                    /** @noinspection PhpUndefinedMethodInspection */
                    if (!$cp->canEditPagePermissions()) {
                        $this->canEdit = false;
                    }
                }
            } else {
                $this->canEdit = false;
            }
        }
    }

    protected function canAccess()
    {
        return $this->config->get('concrete.permissions.model') !== 'simple' && $this->canEdit;
    }

    private function isPermissionsInheritOverride()
    {
        return $this->permissionsInherit === 'OVERRIDE';
    }

    public function getAllAccessEntities()
    {
        $response = new EditResponse();
        $errorList = new ErrorList();

        $accessEntityItems = [];

        if (count($this->pages) === 0) {
            $errorList->add(t("There are no pages selected."));
        } else if (!$this->isPermissionsInheritOverride()) {
            $errorList->add(t("You may only remove access to these selected pages if they have all been set to override parent or page defaults permissions."));
        } else {
            $permissionKeyId = (int)$this->request->request->get('permissionKeyId');

            $permissionKey = Key::getByID($permissionKeyId);

            if (!$permissionKey instanceof Key) {
                $errorList->add(t("You need to enter a valid permission key."));
            } else {
                foreach ($this->pages as $page) {
                    $permissionKey->setPermissionObject($page);
                    $permissionKeyAccessObject = $permissionKey->getPermissionAccessObject();

                    if ($permissionKeyAccessObject instanceof PageAccess) {
                        $listItems = $permissionKeyAccessObject->getAccessListItems(Key::ACCESS_TYPE_ALL);

                        foreach ($listItems as $listItem) {
                            $accessEntity = $listItem->getAccessEntityObject();

                            $permissionDurationId = 0;
                            $permissionDurationItem = $listItem->getPermissionDurationObject();

                            if ($permissionDurationItem instanceof Duration) {
                                $permissionDurationId = $permissionDurationItem->getPermissionDurationID();
                            }

                            $accessEntityItem = [
                                "accessId" => $permissionKeyAccessObject->getPermissionAccessID(),
                                "accessEntityId" => $accessEntity->getAccessEntityID(),
                                "accessType" => $listItem->getAccessType(),
                                "permissionDurationId" => $permissionDurationId,
                                "label" => $accessEntity->getAccessEntityLabel()
                            ];

                            if (!in_array($accessEntityItem, $accessEntityItems)) {
                                $accessEntityItems[] = $accessEntityItem;
                            }
                        }
                    }
                }
            }
        }

        $response->setError($errorList);
        $response->setAdditionalDataAttribute("accessEntityItems", $accessEntityItems);

        return $this->responseFactory->json($response);
    }

    public function view($task = null)
    {
        $permissionKeyList = [];

        foreach (Key::getList('page') as $permissionKey) {
            $permissionKeyList[$permissionKey->getPermissionKeyID()] = $permissionKey->getPermissionKeyDisplayName();
        }

        $pageIds = [];

        foreach ($this->pages as $page) {
            $pageIds[] = $page->getCollectionID();
        }

        $this->set('pages', $this->pages);
        $this->set('task', $task);
        $this->set('permissionKeyList', $permissionKeyList);
        $this->set('isPermissionsInheritOverride', $this->isPermissionsInheritOverride());
        $this->set('pageIds', $pageIds);
        $this->set('category', Category::getByHandle('page'));
    }


}
