<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\User\Group\GroupType;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class GroupTypes extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function added()
    {
        $this->set('success', t("The group type has been successfully added."));
        $this->view();
    }

    public function removed()
    {
        $this->set('success', t("The group type has been successfully removed."));
        $this->view();
    }

    public function updated()
    {
        $this->set('success', t("The group type has been successfully updated."));
        $this->view();
    }

    public function remove($groupTypeId = null)
    {
        $groupType = GroupType::getByID($groupTypeId);

        if ($groupType === false) {
            return $this->responseFactory->notFound(t("Invalid Group Type."));
        }

        try {
            $groupType->delete();
        } catch (\Exception $e) {
            $this->error->add($e->getMessage());
        }

        $this->set('groupType', $groupType);

        if (!$this->error->has()) {
            return $this->responseFactory->redirect((string)Url::to("/dashboard/users/group_types/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        }
    }

    public function get_group_type($groupTypeId)
    {
        $groupType = GroupType::getByID($groupTypeId);

        if ($groupType === false) {
            return $this->responseFactory->notFound(t("Invalid Group Type."));
        }

        return new JsonResponse($groupType);
    }

    private function validate()
    {
        /** @var Validation $validator */
        $validator = $this->app->make(Validation::class);
        $validator->addRequiredToken("save_group_type");
        $validator->addRequired("gtName", t("You need to enter a valid name."));
        $validator->setData($this->request->request->all());
        if (!$validator->test()) {
            $this->error = $validator->getError();
            return false;
        } else {
            return true;
        }
    }

    private function validateRoles()
    {
        $hasManagerRole = false;

        if (is_array($this->request->request->get("roles"))) {
            foreach ($this->request->request->get("roles") as $roleId => $role) {
                if (strlen($role["name"]) === 0) {
                    $this->error->add(t("You need to enter a role name."));
                }

                if (isset($role["manager"])) {
                    $hasManagerRole = true;
                }
            }

            if (!$hasManagerRole) {
                //$this->error->add(t("You need to have at least one manager role."));
            }

            if (!in_array($this->request->request->get("defaultRole"), array_keys($this->request->request->get("roles")))) {
                $this->error->add(t("You need to set a default role."));
            }
        } else {
            $this->error->add(t("You need to have at least one role."));
        }

        return !$this->error->has();
    }

    /**
     * @param GroupType $groupType
     */
    private function saveRoles($groupType)
    {
        $newRoles = [];
        $updateRoleIds = [];
        $defaultRole = null;

        foreach ($this->request->request->get("roles") as $roleId => $role) {
            if (substr($roleId, 0, 1) === "_") {
                $newRoles[$roleId] = $role;
            } else {
                $updateRoleIds[$roleId] = $roleId;
            }
        }

        // update existing roles and remove removed items
        foreach ($groupType->getRoles() as $role) {
            if (in_array($role->getId(), array_keys($updateRoleIds))) {
                $updateData = $this->request->request->get("roles")[$role->getId()];
                $role->setName($updateData["name"]);
                $role->setIsManager(isset($updateData["manager"]));

                if ($role->getId() == $this->request->request->get("defaultRole")) {
                    $defaultRole = $role;
                }
            } else {
                try {
                    $role->delete();
                } catch (\Exception $e) {
                    $this->error->add($e->getMessage());
                }
            }
        }

        // append new roles
        foreach ($newRoles as $roleId => $role) {
            $groupRole = GroupRole::add($role["name"], isset($role["manager"]));

            if (is_object($groupRole)) {
                $groupType->addRole($groupRole);
            }

            if ($roleId == $this->request->request->get("defaultRole")) {
                $defaultRole = $groupRole;
            }
        }

        if ($defaultRole !== null) {
            $groupType->setDefaultRole($defaultRole);
        }
    }

    public function edit($groupTypeId = null)
    {
        $groupType = GroupType::getByID($groupTypeId);

        if ($groupType === false) {
            return $this->responseFactory->notFound(t("Invalid Group Type."));
        }

        $this->set('groupType', $groupType);

        if ($this->request->getMethod() === "POST" && $this->validate() && $this->validateRoles()) {
            $groupType->setName($this->request->request->get("gtName"));
            $groupType->setPetitionForPublicEntry($this->request->request->has("gtPetitionForPublicEntry"));

            $this->saveRoles($groupType);

            if (!$this->error->has()) {
                return $this->responseFactory->redirect((string)Url::to("/dashboard/users/group_types/added"), Response::HTTP_TEMPORARY_REDIRECT);
            }
        }
    }

    public function add()
    {
        if ($this->request->getMethod() === "POST" && $this->validate() && $this->validateRoles()) {
            $groupType = GroupType::add($this->request->request->get("gtName"), $this->request->request->has("gtPetitionForPublicEntry"));

            $this->saveRoles($groupType);

            if (!$this->error->has()) {
                return $this->responseFactory->redirect((string)Url::to("/dashboard/users/group_types/updated"), Response::HTTP_TEMPORARY_REDIRECT);
            }
        }
    }

    public function view()
    {
        $this->set('groupTypes', GroupType::getList());
    }
}