<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;


use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\ConversationMessageAuthorEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\GroupRepository;

class SetDefaultConversationPermissionsRoutineHandler
{

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function __invoke()
    {
        $g1 = $this->groupRepository->getGroupById(GUEST_GROUP_ID);
        $g2 = $this->groupRepository->getGroupById(REGISTERED_GROUP_ID);
        $g3 = $this->groupRepository->getGroupById(ADMIN_GROUP_ID);

        $messageAuthorEntity = ConversationMessageAuthorEntity::getOrCreate();
        $guestEntity = GroupEntity::getOrCreate($g1);
        $registeredEntity = GroupEntity::getOrCreate($g2);
        $adminGroupEntity = GroupEntity::getOrCreate($g3);

        $pk = Key::getByHandle('add_conversation_message');
        $pa = Access::create($pk);
        $pa->addListItem($guestEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = Key::getByHandle('add_conversation_message_attachments');
        $pa = Access::create($pk);
        $pa->addListItem($guestEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = Key::getByHandle('edit_conversation_message');
        $pa = Access::create($pk);
        $pa->addListItem($messageAuthorEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = Key::getByHandle('delete_conversation_message');
        $pa = Access::create($pk);
        $pa->addListItem($messageAuthorEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $pk = Key::getByHandle('rate_conversation_message');
        $pa = Access::create($pk);
        $pa->addListItem($registeredEntity);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);

        $permissions = [
            'edit_conversation_permissions',
            'flag_conversation_message',
            'approve_conversation_message',
        ];
        foreach ($permissions as $pkHandle) {
            $pk = Key::getByHandle($pkHandle);
            $pa = Access::create($pk);
            $pa->addListItem($adminGroupEntity);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }


}
