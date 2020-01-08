<?php

namespace Concrete\Core\Logging\Entry\Permission\Assignment;

use Concrete\Core\Logging\Entry\EntryInterface;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\User\User;

/**
 * Log entry for permission assignments
 */
class Assignment implements EntryInterface
{

    /**
     * The access object being granted to the key.
     *
     * @var Access
     */
    protected $access;

    /**
     * The permission key being applied
     *
     * @var Key
     */
    protected $key;

    /**
     * The user applying the permission assignment
     *
     * @var User
     */
    protected $applier;

    /**
     * Assignment constructor.
     * @param User $applier
     * @param Key $key
     * @param Access $access
     */
    public function __construct(User $applier, Key $key, Access $access)
    {
        $this->applier = $applier;
        $this->key = $key;
        $this->access = $access;
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        $applier = $this->applier->getUserName();
        $permission = $this->key->getPermissionKeyHandle();
        $object = $this->key->getPermissionObject();
        if ($object) {
            /**
             * @var $object ObjectInterface
             */
            $object = $object->getPermissionObjectIdentifier();
            return t('Permission assignment applied for permission %1$s on object %2$s by user %3$s',
                $permission, $object, $applier);
        } else {
            return t('Permission assignment applied for permission %1$s by user %2$s',
                $permission, $applier);
        }
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        $listItems = $this->access->getAccessListItems(Key::ACCESS_TYPE_ALL);
        $loggedItems = [];
        foreach ($listItems as $listItem) {
            $type = $listItem->getAccessType();
            $entity = $listItem->getAccessEntityObject();
            $loggedItems[] = [
                'id' => $entity->getAccessEntityID(),
                'access_type' => $type,
                'entity_type' => $entity->getAccessEntityTypeHandle(),
                'entity_name' => $entity->getAccessEntityLabel()
            ];
        }
        $object = $this->key->getPermissionObject();
        $return = [];
        $return['applier'] = $this->applier->getUserName();
        $return['handle'] = $this->key->getPermissionKeyHandle();
        $return['category'] = $this->key->getPermissionKeyCategoryHandle();
        if ($object) {
            $return['object_id'] = $object->getPermissionObjectIdentifier();
        }
        $return['entities'] = $loggedItems;
        return $return;
    }
}
