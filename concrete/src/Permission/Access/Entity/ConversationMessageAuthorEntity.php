<?php

namespace Concrete\Core\Permission\Access\Entity;

use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Permission\Access\Access as PermissionAccess;
use Concrete\Core\Permission\Access\ConversationAccess;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;

class ConversationMessageAuthorEntity extends Entity
{
    /**
     * @param PermissionAccess $pa
     *
     * @return \Concrete\Core\User\UserInfo[]
     */
    public function getAccessEntityUsers(PermissionAccess $pa)
    {
        $message = $pa->getPermissionObject();
        if (is_object($message) && ($message instanceof Message)) {
            return app(UserInfoRepository::class)->getByID($message->getConversationMessageUserID());
        }

        return [];
    }

    /**
     * @param PermissionAccess $pae
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return bool
     */
    public function validate(PermissionAccess $pae)
    {
        $message = null;
        if ($pae instanceof ConversationAccess) {
            $message = $pae->getPermissionObject();
        }

        if ($message instanceof Message) {
            $app = Application::getFacadeApplication();
            $u = $app->make(User::class);

            return $u->getUserID() == $message->getConversationMessageUserID();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getAccessEntityTypeLinkHTML()
    {
        return '<a href="javascript:void(0)" class="dropdown-item" onclick="ccm_choosePermissionAccessEntityConversationMessageAuthor()">' . tc(
            'PermissionAccessEntityTypeName',
            'Message Author'
        ) . '</a>';
    }

    /**
     * @param User|\Concrete\Core\Entity\User\User $user
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return Entity[]
     */
    public static function getAccessEntitiesForUser($user)
    {
        $entities = [];
        /** @var Connection $db */
        $db = app(Connection::class);
        if (is_object($user) && $user->isRegistered()) {
            $pae = static::getOrCreate();
            $r = $db->fetchOne('select cnvMessageID from ConversationMessages where uID = ?', [$user->getUserID()]);
            if ($r > 0) {
                $entities[] = $pae;
            }
        }

        return $entities;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @return Entity|false
     */
    public static function getOrCreate()
    {
        /** @var Connection $db */
        $db = app(Connection::class);
        $petID = $db->fetchOne('select petID from PermissionAccessEntityTypes where petHandle = \'conversation_message_author\'');
        $peID = $db->fetchOne(
            'select peID from PermissionAccessEntities where petID = ?',
            [$petID]
        );
        if (!$peID) {
            $db->executeStatement('insert into PermissionAccessEntities (petID) values(?)', [$petID]);
            $peID = $db->lastInsertId();
            app('config')->save('concrete.misc.access_entity_updated', time());
        }

        return \Concrete\Core\Permission\Access\Entity\Entity::getByID($peID);
    }

    /**
     * @return void
     */
    public function load()
    {
        $this->label = t('Message Author');
    }
}
