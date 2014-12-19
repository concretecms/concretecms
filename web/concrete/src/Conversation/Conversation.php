<?php
namespace Concrete\Core\Conversation;
use Concrete\Core\Search\PermissionableListItemInterface;
use Loader;
use \Concrete\Core\Foundation\Object;
use Page;
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;

class Conversation extends Object implements \Concrete\Core\Permission\ObjectInterface {

    const POSTING_ENABLED = 10;
    const POSTING_DISABLED_MANUALLY = 5;
    const POSTING_DISABLED_PERMISSIONS = 3;

    public function getConversationID() {return $this->cnvID;}
	public function getConversationParentMessageID() {return $this->cnvParentMessageID;}
	public function getConversationDateCreated() {return $this->cnvDateCreated;}
	public function getConversationDateLastMessage() {return $this->cnvDateLastMessage;}
	public function getConversationMessagesTotal() {return intval($this->cnvMessagesTotal);}
    public function getConversationMaxFileSizeGuest() { return intval($this->cnvMaxFileSizeGuest);}
    public function getConversationMaxFileSizeRegistered() { return intval($this->cnvMaxFileSizeRegistered);}
    public function getConversationMaxFilesGuest() { return intval($this->cnvMaxFilesGuest);}
    public function getConversationMaxFilesRegistered() { return intval($this->cnvMaxFilesRegistered);}
    public function getConversationFileExtensions() { return $this->cnvFileExtensions;}
    public function getConversationAttachmentOverridesEnabled() { return intval($this->cnvAttachmentOverridesEnabled);}
    public function getConversationAttachmentsEnabled() { return intval($this->cnvAttachmentsEnabled);}

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

    public static function getByID($cnvID) {
		$db = Loader::db();
		$r = $db->GetRow('select cnvID, cID, cnvDateCreated, cnvDateLastMessage, cnvMessagesTotal, cnvAttachmentsEnabled, cnvAttachmentOverridesEnabled,
		cnvFileExtensions, cnvMaxFileSizeRegistered, cnvMaxFileSizeGuest, cnvMaxFilesRegistered, cnvMaxFilesGuest, cnvOverrideGlobalPermissions from Conversations where cnvID = ?', array($cnvID));
		if (is_array($r) && $r['cnvID'] == $cnvID) {
			$cnv = new static;
			$cnv->setPropertiesFromArray($r);
			return $cnv;
		}
	}

	public function getConversationPageObject() {
		if ($this->cID) {
			$c = Page::getByID($this->cID, 'ACTIVE');
			if (is_object($c) && !$c->isError()) {
				return $c;
			}
		}
	}

	public function setConversationPageObject($c) {
		$db = Loader::db();
		$db->Execute('update Conversations set cID = ? where cnvID = ?', array($c->getCollectionID(), $this->getConversationID()));
		$this->cID = $c->getCollectionID();
	}

	public function updateConversationSummary() {
		$db = Loader::db();
		$date = $db->GetOne('select max(cnvMessageDateCreated) from ConversationMessages where cnvID =  ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1', array(
			$this->getConversationID()
		));
		if (!$date) {
			$date = $this->getConversationDateCreated();
		}

		$total = $db->GetOne('select count(cnvMessageID) from ConversationMessages where cnvID = ? and cnvIsMessageDeleted = 0 and cnvIsMessageApproved = 1', array($this->cnvID));
		$db->Execute('update Conversations set cnvDateLastMessage = ?, cnvMessagesTotal = ? where cnvID = ?', array(
			$date, $total, $this->getConversationID()
		));
	}

    /**
     * @return \Concrete\Core\User\UserInfo[]
     */
    public function getConversationMessageUsers() {
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

	public function setConversationParentMessageID($cnvParentMessageID) {
		$db = Loader::db();
		$db->Execute('update Conversations set cnvParentMessageID = ? where cnvID = ?', array($cnvParentMessageID, $this->getConversationID()));
		$this->cnvMessageParentID = $cnvParentMessageID;
	}

	public static function add() {
		$db = Loader::db();
		$date = Loader::helper('date')->getOverridableNow();
		$r = $db->Execute('insert into Conversations (cnvDateCreated, cnvDateLastMessage) values (?, ?)', array($date, $date));
		return static::getByID($db->Insert_ID());
	}

    public function setConversationAttachmentOverridesEnabled($cnvAttachmentOverridesEnabled) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvAttachmentOverridesEnabled = ? where cnvID = ?', array(intval($cnvAttachmentOverridesEnabled), $this->getConversationID()));
    }

    public function setConversationAttachmentsEnabled($cnvAttachmentsEnabled) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvAttachmentsEnabled = ? where cnvID = ?', array(intval($cnvAttachmentsEnabled), $this->getConversationID()));
    }

    public function setConversationMaxFileSizeGuest($cnvMaxFileSizeGuest) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFileSizeGuest = ? where cnvID = ?', array(intval($cnvMaxFileSizeGuest), $this->getConversationID()));
    }

    public function setConversationMaxFileSizeRegistered($cnvMaxFileSizeRegistered) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFileSizeRegistered = ? where cnvID = ?', array(intval($cnvMaxFileSizeRegistered), $this->getConversationID()));
    }

    public function setConversationMaxFilesGuest($cnvMaxFilesGuest) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFilesGuest = ? where cnvID = ?', array(intval($cnvMaxFilesGuest), $this->getConversationID()));
    }

    public function setConversationMaxFilesRegistered($cnvMaxFilesRegistered) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvMaxFilesRegistered = ? where cnvID = ?', array(intval($cnvMaxFilesRegistered), $this->getConversationID()));
    }

    public function setConversationFileExtensions($cnvFileExtensions) {
        $db = Loader::db();
        $db->Execute('update Conversations set cnvFileExtensions = ? where cnvID = ?', array($cnvFileExtensions, $this->getConversationID()));
    }




}
