<?php

namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Controller\Traits\DashboardBulkUpdaterTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Symfony\Component\HttpFoundation\JsonResponse;

class Properties extends BackendInterfaceController
{
    use KeySelectorControllerTrait;
    use DashboardBulkUpdaterTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/page/bulk/properties';

    /**
     * @var CategoryInterface
     */
    protected $category;

    protected function getObjectFromRequestId(string $id)
    {
        $page = Page::getByID($id, 'RECENT');
        if ($page && !$page->isError()) {
            return $page;
        }
        return null;
    }

    public function canPerformOperationOnObject($object): bool
    {
        $checker = new Checker($object);
        return $checker->canEditPageProperties();
    }

    public function __construct(CategoryService $attributeCategoryService)
    {
        parent::__construct();

        $categoryEntity = $attributeCategoryService->getByHandle('collection');
        $this->category = $categoryEntity->getAttributeKeyCategory();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_start()
     */
    public function on_start()
    {
        parent::on_start();

        $this->populateItemsFromRequest();
    }

    public function view()
    {
        $keySelector = $this->app->make(ElementManager::class)->get('attribute/component/key_selector', [
            'category' => $this->getCategory()
        ]);
        /** @var \Concrete\Controller\Element\Attribute\Component\KeySelector $controller */
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObjects($this->getObjects());

        $this->set('pages', $this->items);
        $this->set('keySelector', $keySelector);
        $this->set('form', $this->app->make('helper/form'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $r = new PageEditResponse();
            $r->setPages($this->items);
            if ($attributesResponse instanceof ErrorList) {
                $r->setError($attributesResponse);
            } else {
                $r->setMessage(t('Attributes updated successfully.'));
            }

            return new JsonResponse($r);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getObjects()
     */
    public function getObjects(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getCategory()
     */
    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::canEditAttributeKey()
     */
    public function canEditAttributeKey(int $akID, ObjectInterface $object): bool
    {
        $attributeKey = $this->category->getAttributeKeyByID($akID);
        $key = Key::getByHandle('edit_page_properties');
        $key->setPermissionObject($object);
        $assignment = $key->getMyAssignment();
        return $assignment->canEditAttributeKey($attributeKey);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        $checker = new Checker(Page::getByPath('/dashboard/sitemap/search'));
        if ($checker->canViewPage()) {
            return $this->getAction() === 'getAttribute' || $this->canEdit;
        }
        return false;
    }
}
