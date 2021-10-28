<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\SinglePage\Dashboard\Users;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Node\Type\GroupFolder as GroupFolderTreeNode;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRole;
use Concrete\Core\User\Group\GroupType;

class AddGroup extends DashboardPageController
{

    /**
     * @return ErrorList
     */
    public function validateRoles()
    {
        $errorList = new ErrorList();

        if ((bool)$this->request->request->get('gOverrideGroupTypeSettings')) {
            $hasManagerRole = false;

            if (is_array($this->request->request->get("roles"))) {
                foreach ($this->request->request->get("roles") as $roleId => $role) {
                    if (strlen($role["name"]) === 0) {
                        $errorList->add(t("You need to enter a role name."));
                    }

                    if (isset($role["manager"])) {
                        $hasManagerRole = true;
                    }
                }

                if (!$hasManagerRole) {
                    //$errorList->add(t("You need to have at least one manager role."));
                }

                if (!in_array($this->request->request->get("defaultRole"), array_keys($this->request->request->get("roles")))) {
                    $errorList->add(t("You need to set a default role."));
                }
            } else {
                $errorList->add(t("You need to have at least one role."));
            }
        }

        return $errorList;
    }

    /**
     * @param Group $g
     */
    public function checkGroupTypeOptions($g)
    {
        $typeWasInherited = !$g->getOverrideGroupTypeSettings();

        $g->setOverrideGroupTypeSettings((bool)$this->request->request->get('gOverrideGroupTypeSettings'));

        if ($this->request->request->has('gThumbnailFID')) {
            $thumbnailFileId = $this->request->request->get('gThumbnailFID');
            $fileEntity = File::getByID($thumbnailFileId);

            if ($fileEntity instanceof \Concrete\Core\Entity\File\File) {
                $g->setThumbnailImage($fileEntity);
            } else {
                $g->removeThumbnailImage();
            }
        } else {
            $g->removeThumbnailImage();
        }

        $g->setPetitionForPublicEntry($this->request->request->has('gtPetitionForPublicEntry'));

        $defaultGroupType = GroupType::getByID(DEFAULT_GROUP_TYPE_ID);

        if ($this->request->post('gtID')) {
            $groupType = GroupType::getByID($this->request->post('gtID'));

            if ($groupType === false) {
                $g->setGroupType($defaultGroupType);
            } else {
                $g->setGroupType($groupType);
            }

        } else {
            $g->setGroupType($defaultGroupType);
        }

        // Update Roles
        if ($g->getOverrideGroupTypeSettings()) {
            $newRoles = [];
            $updateRoleIds = [];
            $defaultRole = null;

            if ($typeWasInherited) {
                $newRoles = $this->request->request->get("roles");
            } else {
                foreach ($this->request->request->get("roles") as $roleId => $role) {
                    if (substr($roleId, 0, 1) === "_") {
                        $newRoles[$roleId] = $role;
                    } else {
                        $updateRoleIds[$roleId] = $roleId;
                    }
                }
            }

            // update existing roles and remove removed items
            foreach ($g->getRoles() as $role) {
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
                    $g->addRole($groupRole);
                }

                if ($roleId == $this->request->request->get("defaultRole")) {
                    $defaultRole = $groupRole;
                }
            }

            if ($defaultRole !== null) {
                $g->setDefaultRole($defaultRole);
            }
        }
    }

    public function checkExpirationOptions($g)
    {
        if ($this->request->post('gUserExpirationIsEnabled')) {
            $date = $this->app->make('helper/form/date_time');
            switch ($this->request->post('gUserExpirationMethod')) {
                case 'SET_TIME':
                    $g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $this->request->post('gUserExpirationAction'));
                    break;
                case 'INTERVAL':
                    $g->setGroupExpirationByInterval($this->request->post('gUserExpirationIntervalDays'), $this->request->post('gUserExpirationIntervalHours'), $this->request->post('gUserExpirationIntervalMinutes'), $this->request->post('gUserExpirationAction'));
                    break;
            }
        } else {
            $g->removeGroupExpiration();
        }
    }

    /**
     * @deprecated
     * @param $g
     */
    public function checkBadgeOptions($g)
    {
        if ($this->request->post('gIsBadge')) {
            $g->setBadgeOptions($this->post('gBadgeFID'), $this->post('gBadgeDescription'), $this->post('gBadgeCommunityPointValue'));
        } else {
            $g->clearBadgeOptions();
        }
    }

    public function checkAutomationOptions($g)
    {
        if ($this->request->post('gIsAutomated')) {
            $g->setAutomationOptions($this->post('gCheckAutomationOnRegister'), $this->post('gCheckAutomationOnLogin'), $this->post('gCheckAutomationOnJobRun'));
        } else {
            $g->clearAutomationOptions();
        }
    }

    public function view()
    {
        $tree = GroupTree::get();
        $this->set('tree', $tree);
    }

    public function do_add()
    {
        $txt = $this->app->make('helper/text');
        $valt = $this->app->make('helper/validation/token');
        $gName = $txt->sanitize($this->request->post('gName'));
        $gDescription = $this->request->post('gDescription');

        if (!$gName) {
            $this->error->add(t("Name required."));
        }

        if (!$valt->validate('add_or_update_group')) {
            $this->error->add($valt->getErrorMessage());
        }

        $parentNode = null;
        if ($this->request->post('gParentNodeID')) {
            $parentNode = TreeNode::getByID($this->request->post('gParentNodeID'));
        }

        if (is_object($parentNode)) {
            $pp = new \Permissions($parentNode);
            if (!$pp->canAddTreeSubNode()) {
                $this->error->add(t('You do not have permission to add a group beneath %s', $parentNode->getTreeNodeDisplayName()));
            }
        }

        foreach($this->validateRoles()->getList() as $error) {
            $this->error->add($error);
        }

        if ($parentNode instanceof GroupFolder) {
            switch ($parentNode->getContains()) {
                case GroupFolder::CONTAINS_GROUP_FOLDERS:
                    $this->error->add(t("You can't create a group beneath the selected parent folder."));
                    break;

                case GroupFolder::CONTAINS_SPECIFIC_GROUPS:
                    $isGroupTypeAllowed = false;

                    if ($this->request->post('gtID')) {
                        $groupType = GroupType::getByID($this->request->post('gtID'));
                        if (is_object($groupType)) {
                            foreach ($parentNode->getSelectedGroupTypes() as $allowedGroupType) {
                                if ($groupType->getId() == $allowedGroupType->getId()) {
                                    $isGroupTypeAllowed = true;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$isGroupTypeAllowed) {
                        $this->error->add(
                            t("You can't create a group of this group type beneath the selected parent folder.")
                        );
                    }

                    break;
            }
        }

        if (!$this->error->has()) {
            $g = Group::addBeneathFolder($gName, $this->request->post('gDescription'), $parentNode);

            $this->checkExpirationOptions($g);
            $this->checkBadgeOptions($g);
            $this->checkAutomationOptions($g);
            $this->checkGroupTypeOptions($g);
            $this->redirect('/dashboard/users/groups', 'group_added');
        }
        $this->view();
    }
}
