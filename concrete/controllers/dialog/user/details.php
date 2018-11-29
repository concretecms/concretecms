<?php

namespace Concrete\Controller\Dialog\User;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\Service\User;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\User\UserInfoRepository;


class Details extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/user/details';

    /**
     * @var UserInfoRepository
     */
    protected $repository;

    /**
     * @var CategoryService
     */
    protected $attributeCategoryService;

    public function __construct(UserInfoRepository $repository, CategoryService $attributeCategoryService)
    {
        parent::__construct();
        $this->repository = $repository;
        $this->attributeCategoryService = $attributeCategoryService;
    }

    public function canAccess()
    {
        $service = new User();
        if (!$service->canAccessUserSearchInterface()) {
            return false;
        }
        $user = $this->getUserFromRequest();
        $permissions = new \Permissions($user);
        return $permissions->canViewUser();
    }

    protected function getUserFromRequest()
    {
        $user = null;
        if ($this->request->query->has('uID')) {
            $user = $this->repository->getByID($this->request->query->get('uID'));
        }

        if (!$user) {
            throw new UserMessageException(t('Invalid user id.'));
        } else {
            return $user;
        }
    }

    public function view()
    {
        $user = $this->getUserFromRequest();
        $categoryEntity = $this->attributeCategoryService->getByHandle('user');
        $category = $categoryEntity->getController();
        $setManager = $category->getSetManager();
        $attributeSets = $setManager->getAttributeSets();
        $unassigned = $setManager->getUnassignedAttributeKeys();
        $this->set('user', $user);
        $this->set('userGroups', $user->getUserObject()->getUserGroupObjects());
        $this->set('uEmail', $user->getUserEmail());
        $this->set('uName', $user->getUserName());
        $this->set('attributeSets', $attributeSets);
        $this->set('unassigned', $unassigned);
    }
}


