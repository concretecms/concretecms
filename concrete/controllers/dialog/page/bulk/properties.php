<?php

namespace Concrete\Controller\Dialog\Page\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
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

    /**
     * List of pages to edit.
     *
     * @var array
     */
    protected $pages = [];

    /**
     * Define whether the user can edit page properties.
     *
     * @var bool
     */
    protected $canEdit = false;

    /**
     * @var int[]
     */
    protected $allowedEditAttributes = [];

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

        $this->populatePages();
        $this->setupAllowedEditAttributes();
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

        $this->set('pages', $this->pages);
        $this->set('keySelector', $keySelector);
        $this->set('form', $this->app->make('helper/form'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $r = new PageEditResponse();
            $r->setPages($this->pages);
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
        return $this->pages;
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
    public function canEditAttributeKey(int $akID): bool
    {
        return in_array($akID, $this->allowedEditAttributes);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    protected function canAccess()
    {
        return ($this->getAction() === 'getAttribute' || $this->canEdit) && count($this->allowedEditAttributes) > 0;
    }

    protected function populatePages(): void
    {
        $items = $this->request->get('item');
        if (is_array($items)) {
            foreach ($items as $cID) {
                $c = Page::getByID($cID);
                if (is_object($c) && !$c->isError()) {
                    $this->pages[] = $c;
                }
            }
        }

        if (count($this->pages) > 0) {
            $this->canEdit = true;
            foreach ($this->pages as $c) {
                $cp = new Checker($c);
                if (!$cp->canEditPageProperties()) {
                    $this->canEdit = false;

                    break;
                }
            }
        } else {
            $this->canEdit = false;
        }
    }

    protected function setupAllowedEditAttributes(): void
    {
        $pk = Key::getByHandle('edit_page_properties');
        $assignment = $pk->getMyAssignment();
        if ($assignment) {
            $this->allowedEditAttributes = $assignment->getAttributesAllowedArray();
        }
    }
}
