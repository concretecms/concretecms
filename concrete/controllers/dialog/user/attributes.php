<?php

namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Controller\Element\Attribute\Component\KeySelector;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Application\Service\User;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Command\ClearAttributesCommand;
use Concrete\Core\Attribute\Command\SaveAttributesCommand;
use Concrete\Core\Attribute\Key\Component\KeySelector\ControllerTrait;
use Concrete\Core\Attribute\Key\Component\KeySelector\KeySerializer;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class Attributes extends BackendInterfaceController
{

    use ControllerTrait;

    protected $viewPath = '/dialogs/user/attributes';

    /**
     * @var UserInfoRepository
     */
    protected $repository;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var UserInfo|null
     */
    protected $requestUser;

    /**
     * @var string[]
     */
    protected $allowedEditAttributes;

    public function __construct(
        UserInfoRepository $repository,
        CategoryService $attributeCategoryService,
        JsonSerializer $serializer
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->serializer = $serializer;
        $categoryEntity = $attributeCategoryService->getByHandle('user');
        $this->category = $categoryEntity->getController();
    }

    public function canAccess()
    {
        $user = $this->getUserFromRequest();
        $permissions = new \Permissions($user);
        if ($permissions->canEditUser()) {
            $pk = Key::getByHandle('edit_user_properties');
            $assignment = $pk->getMyAssignment();
            if ($assignment) {
                $this->allowedEditAttributes = $assignment->getAttributesAllowedArray();
                return count($this->allowedEditAttributes) > 0;
            }
        }
        return false;
    }

    protected function getUserFromRequest()
    {
        if (!isset($this->requestUser)) {
            if ($this->request->attributes->has('uID')) {
                $this->requestUser = $this->repository->getByID($this->request->attributes->get('uID'));
            }
        }

        if (!$this->requestUser) {
            throw new UserMessageException(t('Invalid user id.'));
        } else {
            return $this->requestUser;
        }
    }

    public function getObjects(): array
    {
        return [$this->getUserFromRequest()];
    }

    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }

    public function canEditAttributeKey(int $akID): bool
    {
        return in_array($akID, $this->allowedEditAttributes);
    }

    public function view($uID)
    {
        $keySelector = $this->app->make(ElementManager::class)
            ->get('attribute/component/key_selector', ['category' => $this->category]);
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

}


