<?php

namespace Concrete\Controller\Dialog\Type;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Symfony\Component\HttpFoundation\JsonResponse;

class Attributes extends BackendInterfaceController
{
    use KeySelectorControllerTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/type/attributes';

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var PageType|null
     */
    protected $requestPageType;

    /**
     * @var int[]
     */
    protected $allowedEditAttributes;

    /**
     * @var Page
     */
    private $defaultPage;

    public function __construct(CategoryService $attributeCategoryService)
    {
        parent::__construct();

        $categoryEntity = $attributeCategoryService->getByHandle('collection');
        $this->category = $categoryEntity->getAttributeKeyCategory();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $permissions = new Checker($this->getPageTypeDefaultPage());
        if ($permissions->canEditPageProperties()) {
            $pk = Key::getByHandle('edit_page_properties');
            $assignment = $pk->getMyAssignment();
            if ($assignment) {
                $this->allowedEditAttributes = $assignment->getAttributesAllowedArray();

                return count($this->allowedEditAttributes) > 0;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait::getObjects()
     */
    public function getObjects(): array
    {
        return [$this->getPageTypeDefaultPage()];
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

    public function view($ptID)
    {
        $keySelector = $this->app->make(ElementManager::class)->get('attribute/component/key_selector', [
            'category' => $this->getCategory()
        ]);
        /** @var \Concrete\Controller\Element\Attribute\Component\KeySelector $controller */
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObjects($this->getObjects());
        $this->set('keySelector', $keySelector);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $message = new EditResponse();
            if ($attributesResponse instanceof ErrorList) {
                $message->setError($attributesResponse);
            } else {
                $message->setMessage(t('Attributes updated successfully.'));
            }
            return new JsonResponse($message);
        }
    }

    protected function getPageTypeDefaultPage(): Page
    {
        if (!$this->defaultPage) {
            $this->defaultPage = $this->getPageTypeFromRequest()->getPageTypePageTemplateDefaultPageObject();
        }

        return $this->defaultPage;
    }

    protected function getPageTypeFromRequest()
    {
        if (!isset($this->requestPageType)) {
            if ($this->request->attributes->has('ptID')) {
                $this->requestPageType = PageType::getByID($this->request->attributes->get('ptID'));
            }
        }

        if (!$this->requestPageType) {
            throw new UserMessageException(t('Invalid page type id.'));
        }

        return $this->requestPageType;
    }
}
