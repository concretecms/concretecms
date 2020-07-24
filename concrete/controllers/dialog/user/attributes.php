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
use Concrete\Core\Attribute\Key\Component\KeySelector\KeySerializer;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class Attributes extends BackendInterfaceController
{

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

    public function view($uID)
    {
        $user = $this->getUserFromRequest();
        $keySelector = $this->app->make(ElementManager::class)
            ->get('attribute/component/key_selector', [$this->category]);
        $controller = $keySelector->getElementController();
        $controller->setSelectAttributeUrl($this->action('get_attribute'));
        $controller->setObject($user);
        $this->set('keySelector', $keySelector);
    }

    public function saveAttributes()
    {
        // Let's retrieve a list of attribute keys that we're trying to set.
        $selectedAttributes = (array) $this->request->request->get('selectedKeys');

        // Now, let's divide attributes into piles of those we need to save, and those we need to clear
        $attributesToClear = [];
        $attributesToSave = [];

        $user = $this->getUserFromRequest();
        $values = $this->category->getAttributeValues($user);
        foreach ($values as $value) {
            $attributeKey = $value->getAttributeKey();
            if ($attributeKey) {
                if (!in_array($attributeKey->getAttributeKeyID(), $selectedAttributes) &&
                    in_array($attributeKey->getAttributeKeyID(), $this->allowedEditAttributes)) {
                    // This is an attribute we have currently set on the object, but it's not
                    // in the request, and it is something we're allowed to edit, so that means it needs
                    // to be cleared
                    $attributesToClear[] = $attributeKey;
                }
            }
        }

        foreach($selectedAttributes as $akID) {
            if (in_array($akID, $this->allowedEditAttributes)) {
                $ak = $this->category->getAttributeKeyByID($akID);
                if ($ak) {
                    $attributesToSave[] = $ak;
                }
            }
        }

        $this->app->executeCommand(new ClearAttributesCommand($attributesToClear, $user));
        $this->app->executeCommand(new SaveAttributesCommand($attributesToSave, $user));

        $message = new EditResponse();
        $message->setMessage(t('Attributes updated successfully.'));
        return new JsonResponse($message);
    }

    public function getAttribute()
    {
        $key = $this->category->getByID($this->request->request->get('akID'));
        $keySerializer = new KeySerializer($key);
        return new JsonResponse($keySerializer);
    }

}


