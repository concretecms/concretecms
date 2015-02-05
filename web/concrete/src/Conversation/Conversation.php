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

    public function getConversationNotificationEnabled()
    {
        if ($this->getConversationNotificationOverridesEnabled() > 0) {
            return (bool) $this->cnvSendNotification;
        } else {
            return Config::get('conversations.notification');
        }
    }

    public function getConversationNotificationEmailAddress()
    {
        if ($this->getConversationNotificationOverridesEnabled() > 0) {
            return $this->cnvNotificationEmailAddress;
        } else {
            return Config::get('conversations.notification_email');
        }
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
		cnvNotificationOverridesEnabled, cnvSendNotification, cnvNotificationEmailAddress from Conversations where cnvID = ?',
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
            if ($ui instanceof UserInfo) {
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

    public function setConversationNotificationEnabled($cnvSendNotification)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvSendNotification = ? where cnvID = ?',
            array(intval($cnvSendNotification), $this->getConversationID()));
    }

    public function setConversationNotificationEmailAddress($cnvNotificationEmailAddress)
    {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvNotificationEmailAddress = ? where cnvID = ?',
            array($cnvNotificationEmailAddress, $this->getConversationID()));
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


}
