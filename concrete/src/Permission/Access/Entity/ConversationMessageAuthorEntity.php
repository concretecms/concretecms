<?php
namespace Concrete\Core\Permission\Access\Entity;

use Loader;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Config;
use UserInfo;
use User;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Permission\Access\ConversationAccess;

class ConversationMessageAuthorEntity extends Entity
{
    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $message = $pa->getPermissionObject();
        if (is_object($message) && ($message instanceof Message)) {
            return UserInfo::getByID($message->getConversationMessageUserID());
        }
    }

    public function validate(PermissionAccess $pae)
    {
        if ($pae instanceof ConversationAccess) {
            $message = $pae->getPermissionObject();
        }

        if ($message instanceof Message) {
            $u = new User();

            return $u->getUserID() == $message->getConversationMessageUserID();
        }

        return false;
    }

    public function getAccessEntityTypeLinkHTML()
    {
        $html = '<a href="javascript:void(0)" onclick="ccm_choosePermissionAccessEntityConversationMessageAuthor()">' . tc(
                'PermissionAccessEntityTypeName',
                'Message Author'
            ) . '</a>';

        return $html;
    }

    public static function getAccessEntitiesForUser($user)
    {
        $entities = array();
        $db = Loader::db();
        if ($user->isRegistered()) {
            $pae = static::getOrCreate();
            $r = $db->GetOne('select cnvMessageID from ConversationMessages where uID = ?', array($user->getUserID()));
            if ($r > 0) {
                $entities[] = $pae;
            }
        }

        return $entities;
    }

    public static function getOrCreate()
    {
        $db = Loader::db();
        $petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'conversation_message_author\'');
        $peID = $db->GetOne(
            'select peID from PermissionAccessEntities where petID = ?',
            array($petID)
        );
        if (!$peID) {
            $db->Execute("insert into PermissionAccessEntities (petID) values(?)", array($petID));
            $peID = $db->Insert_ID();
            Config::save('concrete.misc.access_entity_updated', time());
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
    }

    public function load()
    {
        $db = Loader::db();
        $this->label = t('Message Author');
    }
}
