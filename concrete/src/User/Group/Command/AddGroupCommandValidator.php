<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\Utility\Service\Text;
use Concrete\Core\Tree\Node\Type\Group as GroupNode;
use Concrete\Core\Tree\Type\Group as GroupTree;

class AddGroupCommandValidator
{

    protected $textService;

    protected $groupRepository;

    /**
     * Whether to use the permissions checker to validate against the currently logged in user.
     * @var bool
     */
    protected $checkPermissions = false;

    public function __construct(GroupRepository $groupRepository, Text $textService)
    {
        $this->textService = $textService;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param bool $checkPermissions
     */
    public function setCheckPermissions(bool $checkPermissions): void
    {
        $this->checkPermissions = $checkPermissions;
    }

    public function validate(AddGroupCommand $command, ErrorList $errorList)
    {
        $gName = $this->textService->sanitize($command->getName());

        if (!$gName) {
            $errorList->add(t("Name required."));
        }

        if ($this->checkPermissions) {
            if ($command->getParentGroupID()) {
                $parentGroupNode = GroupNode::getTreeNodeByGroupID($command->getParentGroupID());
            } else {
                $parentGroupNode = (GroupTree::get())->getRootTreeNodeObject();
            }

            $pp = new Checker($parentGroupNode);
            if (!$pp->canAddSubGroup()) {
                $errorList->add(t('You do not have permission to add a group at this location.'));
            }
        }

        return $errorList;
    }


}