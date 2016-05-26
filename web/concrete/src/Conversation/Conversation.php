<?php
namespace Concrete\Core\Conversation;

use Concrete\Core\Search\PermissionableListItemInterface;
use Loader;
use \Concrete\Core\Foundation\Object;
use Page;
use Config;
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;

class Conversation extends Object implements \Concrete\Core\Permission\ObjectInterface
{

    const POSTING_ENABLED = 10;
    const POSTING_DISABLED_MANUALLY = 5;
    const POSTING_DISABLED_PERMISSIONS = 3;

    protected $page;

    public function getConversationID()
    {
        return $this->cnvID;
    }

    public function getConversationParentMessageID()
    {
        return $this->cnvParentMessageID;
    }

    public function getConversationDateCreated()
    {
        return $this->cnvDateCreated;
    }

    public function getConversationDateLastMessage()
    {
        return $this->cnvDateLastMessage;
    }

    public function getConversationMessagesTotal()
    {
        return intval($this->cnvMessagesTotal);
    }

    public function getConversationMaxFileSizeGuest()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return intval($this->cnvMaxFileSizeGuest);
        } else {
            return Config::get('conversations.files.guest.max_size');
        }
    }

    public function getConversationMaxFileSizeRegistered()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return intval($this->cnvMaxFileSizeRegistered);
        } else {
            return Config::get('conversations.files.registered.max_size');
        }
    }

    public function getConversationMaxFilesGuest()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return $this->cnvMaxFilesGuest;
        } else {
            return Config::get('conversations.files.guest.max');
        }
    }

    public function getConversationMaxFilesRegistered()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return $this->cnvMaxFilesRegistered;
        } else {
            return Config::get('conversations.files.registered.max');
        }
    }

    public function getConversationFileExtensions()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return $this->cnvFileExtensions;
        } else {
            $fileExtensions = Config::get('conversations.files.allowed_types');
            if (!$fileExtensions) {
                $fileExtensions = Config::get('concrete.upload.extensions');
            }
            return $fileExtensions;
        }
    }


    public function getConversationAttachmentOverridesEnabled()
    {
        return intval($this->cnvAttachmentOverridesEnabled);
    }

    public function getConversationAttachmentsEnabled()
    {
        if ($this->getConversationAttachmentOverridesEnabled() > 0) {
            return (bool)$this->cnvAttachmentsEnabled;
        } else {
            return Config::get('conversations.attachments_enabled');
        }
    }

    public function getConversationNotificationOverridesEnabled()
    {
        return (bool) $this->cnvNotificationOverridesEnabled;
    }

    public function overrideGlobalPermissions()
    {
        return $this->cnvOverrideGlobalPermissions;
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\ConversationResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\ConversationAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'conversation';
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getConversationID();
    }

    public static function getByID($cnvID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select cnvID, cID, cnvDateCreated, cnvDateLastMessage, cnvMessagesTotal, cnvAttachmentsEnabled, cnvAttachmentOverridesEnabled,
		cnvFileExtensions, cnvMaxFileSizeRegistered, cnvMaxFileSizeGuest, cnvMaxFilesRegistered, cnvMaxFilesGuest, cnvOverrideGlobalPermissions,
		cnvNotificationOverridesEnabled, cnvEnableSubscription from Conversations where cnvID = ?',
            array($cnvID));
        if (is_array($r) && $r['cnvID'] == $cnvID) {
            $cnv = new static;
            $cnv->setPropertiesFromArray($r);
            return $cnv;
        }
    }

    public function getConversationPageObject()
    {
        if ($this->cID) {
            $c = Page::getByID($this->cID, 'ACTIVE');
            if (is_object($c) && !$c->isError()) {
                return $c;
            }
        }
    }

    public function setConversationPageObject($c)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cID = ? where cnvID = ?',
            array($c->getCollectionID(), $this->getConversationID()));
        $this->cID = $c->getCollectionID();
    }

    public function updateConversationSummary()
    {
        $db = Loader::db();
        $date = $db->GetOne('select max(cnvMessageDateCreated) from ConversationMessages where cnvID =  ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1',
            array(
                $this->getConversationID()
            ));
        if (!$date) {
            $date = $this->getConversationDateCreated();
        }

        $total = $db->GetOne('select count(cnvMessageID) from ConversationMessages where cnvID = ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1',
            array($this->cnvID));
        $db->Execute('update Conversations set cnvDateLastMessage = ?, cnvMessagesTotal = ? where cnvID = ?', array(
            $date,
            $total,
            $this->getConversationID()
        ));
    }

    /**
     * @return \Concrete\Core\User\UserInfo[]
     */
    public function getConversationMessageUsers()
    {
        $ml = new ConversationMessageList();
        $ml->filterByConversation($this);
        $users = array();
        foreach ($ml->get() as $message) {
            $ui = $message->getConversationMessageUserObject();
            if ($ui instanceof \Concrete\Core\User\UserInfo) {
                $users[$ui->getUserID()] = $ui;
            }
        }
        return array_values($users);
    }

    public function setConversationParentMessageID($cnvParentMessageID)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvParentMessageID = ? where cnvID = ?',
            array($cnvParentMessageID, $this->getConversationID()));
        $this->cnvMessageParentID = $cnvParentMessageID;
    }

    public static function add()
    {
        $db = Loader::db();
        $date = Loader::helper('date')->getOverridableNow();
        $r = $db->Execute('insert into Conversations (cnvDateCreated, cnvDateLastMessage) values (?, ?)',
            array($date, $date));
        return static::getByID($db->Insert_ID());
    }

    public function setConversationAttachmentOverridesEnabled($cnvAttachmentOverridesEnabled)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvAttachmentOverridesEnabled = ? where cnvID = ?',
            array(intval($cnvAttachmentOverridesEnabled), $this->getConversationID()));
    }

    public function setConversationNotificationOverridesEnabled($cnvNotificationOverridesEnabled)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvNotificationOverridesEnabled = ? where cnvID = ?',
            array(intval($cnvNotificationOverridesEnabled), $this->getConversationID()));
    }

    public function setConversationAttachmentsEnabled($cnvAttachmentsEnabled)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvAttachmentsEnabled = ? where cnvID = ?',
            array(intval($cnvAttachmentsEnabled), $this->getConversationID()));
    }

    public function setConversationMaxFileSizeGuest($cnvMaxFileSizeGuest)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFileSizeGuest = ? where cnvID = ?',
            array(intval($cnvMaxFileSizeGuest), $this->getConversationID()));
    }

    public function setConversationMaxFileSizeRegistered($cnvMaxFileSizeRegistered)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFileSizeRegistered = ? where cnvID = ?',
            array(intval($cnvMaxFileSizeRegistered), $this->getConversationID()));
    }

    public function setConversationMaxFilesGuest($cnvMaxFilesGuest)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFilesGuest = ? where cnvID = ?',
            array(intval($cnvMaxFilesGuest), $this->getConversationID()));
    }

    public function setConversationMaxFilesRegistered($cnvMaxFilesRegistered)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFilesRegistered = ? where cnvID = ?',
            array(intval($cnvMaxFilesRegistered), $this->getConversationID()));
    }

    public function setConversationFileExtensions($cnvFileExtensions)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvFileExtensions = ? where cnvID = ?',
            array($cnvFileExtensions, $this->getConversationID()));
    }

    public function getConversationSubscriptionEnabled()
    {
        if ($this->getConversationNotificationOverridesEnabled() > 0) {
            return $this->cnvEnableSubscription;
        } else {
            return Config::get('conversations.subscription_enabled');
        }
    }

    public function setConversationSubscriptionEnabled($cnvEnableSubscription)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvEnableSubscription = ? where cnvID = ?',
            array(intval($cnvEnableSubscription), $this->getConversationID()));
    }

    /**
     * Similar to the method below, but excludes global subscribers who have opted out of conversations, etc...
     * This method should be used any time we actually act on subscriptions, send emails, etc...
     */
    public function getConversationUsersToEmail()
    {
        $db = Loader::db();

        $ids = array();
        if (!$this->getConversationNotificationOverridesEnabled() > 0) {
            $ids = $db->GetCol('select uID from ConversationSubscriptions where cnvID = 0');
        }

        $r = $db->Execute('select uID, type from ConversationSubscriptions where cnvID = ?',
            array($this->getConversationID()));
        while ($row = $r->FetchRow()) {
            if ($row['type'] == 'U' && in_array($row['uID'], $ids)) {
                $ids = array_diff($ids, array($row['uID']));
            } else {
                $ids[] = $row['uID'];
            }
        }

        $ids = array_unique($ids);

        $users = array();
        foreach($ids as $uID) {
            $ui = \UserInfo::getByID($uID);
            if (is_object($ui)) {
                $users[] = $ui;
            }
        }
        return $users;
    }

    public function getConversationSubscribedUsers()
    {
        if ($this->getConversationNotificationOverridesEnabled() > 0) {
            $db = Loader::db();
            $r = $db->GetCol('select uID from ConversationSubscriptions where cnvID = ? order by uID asc',
                array($this->getConversationID()));
            $users = array();
            foreach($r as $uID) {
                $ui = \UserInfo::getByID($uID);
                if (is_object($ui)) {
                    $users[] = $ui;
                }
            }
        } else {
            $users = \Conversation::getDefaultSubscribedUsers();
        }
        return $users;
    }

    public static function getDefaultSubscribedUsers()
    {
        $db = Loader::db();
        $r = $db->GetCol('select uID from ConversationSubscriptions where cnvID = 0 order by uID asc');
        $users = array();
        foreach($r as $uID) {
            $ui = \UserInfo::getByID($uID);
            if (is_object($ui)) {
                $users[] = $ui;
            }
        }
        return $users;
    }

    public function setConversationSubscribedUsers($users)
    {
        $db = Loader::db();
        $db->delete('ConversationSubscriptions', array('cnvID' => $this->getConversationID() ));
        $db->beginTransaction();
        foreach($users as $ui) {
            $db->insert('ConversationSubscriptions', array('cnvID' => $this->getConversationID(), 'uID' => $ui->getUserID()));
        }
        $db->commit();
    }

    public function isUserSubscribed($user)
    {
        $db = Loader::db();
        $type = $db->GetOne('select type from ConversationSubscriptions where uID = ? and cnvID = ?', array(
           $user->getUserID(), $this->getConversationID()
        ));
        if ($type == 'S') {
            return true;
        } else if ($type == 'U') {
            return false;
        } else {
            if ($this->getConversationNotificationOverridesEnabled() > 0) {
                return false;
            } else {
                $cnt = $db->GetOne('select count(cnvID) from ConversationSubscriptions where uID = ? and cnvID = 0', array(
                    $user->getUserID()
                ));
                return $cnt > 0;
            }
        }
    }

    public function subscribeUser($user)
    {
        $db = Loader::db();
        $db->delete('ConversationSubscriptions', array('cnvID' => $this->getConversationID(), 'uID' => $user->getUserID()));
        if (!$this->isUserSubscribed($user)) {
            // note, even though we just deleted the row, we still run the check, because the user COULD be subscribed
            // globally
            $db->insert('ConversationSubscriptions', array('cnvID' => $this->getConversationID(), 'uID' => $user->getUserID()));
        }
    }

    public function unsubscribeUser($user)
    {
        $db = Loader::db();
        $db->delete('ConversationSubscriptions', array('cnvID' => $this->getConversationID(), 'uID' => $user->getUserID()));
        if ($this->isUserSubscribed($user)) {
            // this means we don't have a subscription record in the conversation specific table. So we check to see if user is still subscribed
            // If that's the case, it means they're subscribed globally.
            $db->insert('ConversationSubscriptions', array('cnvID' => $this->getConversationID(), 'type' => 'U', 'uID' => $user->getUserID()));
        }
    }

    public static function setDefaultSubscribedUsers($users)
    {
        $db = Loader::db();
        $db->delete('ConversationSubscriptions', array('cnvID' => 0));
        $db->beginTransaction();
        foreach($users as $ui) {
            $db->insert('ConversationSubscriptions', array('cnvID' => 0, 'uID' => $ui->getUserID()));
        }
        $db->commit();
    }


}
