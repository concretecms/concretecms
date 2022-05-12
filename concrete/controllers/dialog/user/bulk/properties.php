<?php

namespace Concrete\Controller\Dialog\User\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait as KeySelectorControllerTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\User\EditResponse as UserEditResponse;
use Concrete\Core\User\UserInfoRepository;
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
    protected $viewPath = '/dialogs/user/bulk/properties';

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * List of pages to edit.
     *
     * @var array
     */
    protected $users = [];

    /**
     * Define whether the current user can edit user properties.
     *
     * @var bool
     */
    protected $canEdit = false;

    /**
     * @var int[]
     */
    protected $allowedEditAttributes = [];

    public function __construct(CategoryService $attributeCategoryService, UserInfoRepository $userInfoRepository)
    {
        parent::__construct();

        $categoryEntity = $attributeCategoryService->getByHandle('user');
        $this->category = $categoryEntity->getAttributeKeyCategory();
        $this->userInfoRepository = $userInfoRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_start()
     */
    public function on_start()
    {
        parent::on_start();

        $this->populateUsers();
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

        $this->set('users', $this->users);
        $this->set('keySelector', $keySelector);
        $this->set('form', $this->app->make('helper/form'));
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $attributesResponse = $this->saveAttributes();
            $r = new UserEditResponse();
            $r->setUsers($this->users);
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
        return $this->users;
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
        $up = $this->app->make('helper/concrete/user');
        if ($up->canAccessUserSearchInterface()) {
            return ($this->getAction() === 'getAttribute' || $this->canEdit) && count($this->allowedEditAttributes) > 0;
        }

        return false;
    }

    protected function populateUsers(): void
    {
        $items = $this->request->get('item');
        if (is_array($items)) {
            foreach ($items as $uID) {
                $ui = $this->userInfoRepository->getByID($uID);
                if (is_object($ui) && !$ui->isError()) {
                    $this->users[] = $ui;
                }
            }
        }

        if (count($this->users) > 0) {
            $this->canEdit = true;
            foreach ($this->users as $ui) {
                $up = new Checker($ui);
                if (!$up->canEditUser()) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }
    }

    protected function setupAllowedEditAttributes(): void
    {
        $pk = Key::getByHandle('edit_user_properties');
        $assignment = $pk->getMyAssignment();
        if ($assignment) {
            $this->allowedEditAttributes = $assignment->getAttributesAllowedArray();
        }
    }
}
