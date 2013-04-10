<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_Conversation_Message extends Object {
	public function getConversationMessageID() {return $this->cnvMessageID;}
	public function getConversationMessageSubject() {return $this->cnvMessageSubject;}
	public function getConversationMessageBody() {return $this->cnvMessageBody;}
	public function getConversationID() {return $this->cnvID;}
	public function getConversationMessageLevel() {return $this->cnvMessageLevel;}
	public function getConversationMessageParentID() {return $this->cnvMessageParentID;}
	public function getConversationMessageSubmitIP() {return long2ip($this->cnvMessageSubmitIP);}
	public function getConversationMessageSubmitUserAgent() { return $this->cnvMessageSubmitUserAgent;}
	public function isConversationMessageDeleted() {return $this->cnvIsMessageDeleted;}
	public function isConversationMessageFlagged() {return (count($this->getConversationMessageFlagTypes()) > 0);}
	public function isConversationMessageApproved() {return $this->cnvIsMessageApproved;}
	public function getConversationMessageFlagTypes() {
		$db = Loader::db();
		if ($this->cnvMessageFlagTypes) return $this->cnvMessageFlagTypes;
		$flagTypes = $db->GetCol('SELECT cnvMessageFlagTypeID FROM ConversationFlaggedMessages WHERE cnvMessageID=?',array($this->cnvMessageID));
		$flags = array();
		foreach ($flagTypes as $flagType) {
			$flags[] = ConversationFlagType::getByID($flagType);
		}
		$this->cnvMessageFlagTypes = $flags;
		return $flags;
	}
	public function getConversationMessageTotalRatingScore() {return $this->cnvMessageTotalRatingScore;}

	public function conversationMessageHasActiveChildren() {
		$db = Loader::db();
		$children = $db->getCol('SELECT cnvMessageID as cnt FROM ConversationMessages WHERE cnvMessageParentID=?',array($this->cnvMessageID));
		foreach ($children as $childID) {
			$child = ConversationMessage::getByID($childID);
			if (($child->isConversationMessageApproved() && !$child->isConversationMessageDeleted()) || $child->conversationMessageHasActiveChildren()) {
				return true;
			}
		}
		return false;
	}
	public function conversationMessageHasChildren() {
		$db = Loader::db();
		$count = $db->getOne('SELECT COUNT(cnvMessageID) as cnt FROM ConversationMessages WHERE cnvMessageParentID=?',array($this->cnvMessageID));
		return ($count > 0);
	}
	public function approve() {
		$db = Loader::db();
		$db->execute('UPDATE ConversationMessages SET cnvIsMessageApproved=1 WHERE cnvMessageID=?',array($this->cnvMessageID));
		$this->cnvIsMessageApproved = true;
	}
	public function unapprove() {
		$db = Loader::db();
		$db->execute('UPDATE ConversationMessages SET cnvIsMessageApproved=0 WHERE cnvMessageID=?',array($this->cnvMessageID));
		$this->cnvIsMessageApproved = false;
	}
	public function conversationMessageHasFlag($flag) {
		if (!$flag instanceof ConversationFlagType) {
			$flag = ConversationFlagType::getByHandle($flag);
		}
		if ($flag instanceof ConversationFlagType) {
			foreach ($this->getConversationMessageFlagTypes() as $type) {
				if ($flag->getID() == $type->getID()) {
					return true;
				}
			}
		}
		return false;
	}
	public function getConversationMessageBodyOutput() {
		if ($this->cnvIsMessageDeleted) {
			return t('This message has been deleted.');
		} else if (!$this->cnvIsMessageApproved) {
			if ($this->conversationMessageHasFlag('spam')) {
				return t('This message has been flagged as spam.');
			}
			return t('This message is queued for approval.');
		} else {
			$editor = ConversationEditor::getActive();
			return $editor->formatConversationMessageBody($this->getConversationMessageConversationObject(),$this->cnvMessageBody);
		}
	}
	public function getConversationMessageConversationObject() {
		return Conversation::getByID($this->cnvID);
	}
	public function getConversationMessageUserObject() {
		return UserInfo::getByID($this->uID);
	}
	public function getConversationMessageUserID() {
		return $this->uID;
	}
	public function getConversationMessageDateTime() {
		return $this->cnvMessageDateCreated;
	}
	public function getConversationMessageDateTimeOutput() {
		return t('Posted on %s', Loader::helper('date')->date('F d, Y \a\t g:i a', strtotime($this->cnvMessageDateCreated)));
	}
	public function rateMessage(ConversationRatingType $ratingType, $post = array()) {
		$uID = 0; //this needs to be fixed
		$db = Loader::db();
		$cnvRatingTypeID = $db->GetOne('SELECT * FROM ConversationRatingTypes WHERE cnvRatingTypeHandle = ?', array($ratingType->cnvRatingTypeHandle));
		$db->Execute('INSERT INTO ConversationMessageRatings (cnvMessageID, cnvRatingTypeID, timestamp, uID) VALUES (?, ?, ?, ?)', array($this->getConversationMessageID(), $cnvRatingTypeID, date('Y-m-d H:i:s'), $uID));
		$ratingType->adjustConversationMessageRatingTotalScore($this);
	}	
	public function getConversationMessageRating(ConversationRatingType $ratingType) {
		$db = Loader::db();
		$cnt = $db->GetOne('SELECT count(*) from ConversationMessageRatings where cnvRatingTypeID = ? AND cnvMessageID = ?',  array($ratingType->getConversationRatingTypeID(), $this->cnvMessageID));
		return $cnt;
		//$this->updateConversationMessageTotalRating();
	}
	public function updateConversationMessageTotalRating() {
		// stuff to do here...
	}
	public function flag($flagtype) {
		if ($flagtype instanceof ConversationFlagType) {
			$db = Loader::db();
			foreach ($this->getConversationMessageFlagTypes() as $ft) {
				if ($ft->getID() === $flagtype->getID()) {
					return;
				}
			}
			$db->execute('INSERT INTO ConversationFlaggedMessages (cnvMessageFlagTypeID, cnvMessageID) VALUES (?,?)',array($flagtype->getID(),$this->getConversationMessageID()));
			$this->cnvMessageFlagTypes[] = $flagtype;
			return true;
		}
		throw new Exception('Invalid flag type.');
	}
	public static function getByID($cnvMessageID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ConversationMessages where cnvMessageID = ?', array($cnvMessageID));
		if (is_array($r) && $r['cnvMessageID'] == $cnvMessageID) {
			$cnv = new ConversationMessage;
			$cnv->getConversationMessageFlagTypes();
			$cnv->setPropertiesFromArray($r);
			return $cnv;
		}
	}
	
	public function attachFile(File $f, $cnvMessageID) {
		$db = Loader::db();
		if(!is_object($f) || !is_object(ConversationMessage::getByID($cnvMessageID))) {
			return false;
		} else {
			
			$db->Execute('INSERT INTO ConversationMessageAttachments (cnvMessageID, fID) VALUES (?, ?)', array(
				$cnvMessageID,
				$f->getFileID()
			));
		}
	}
	
	public function removeFile($cnvMessageAttachmentID) {
		$db = Loader::db();
		$db->Execute('DELETE FROM ConversationMessageAttachments WHERE cnvMessageAttachmentID = ?', array(
			$cnvMessageAttachmentID
		));
	}
	
	public function getAttachments($cnvMessageID) {
		$db = Loader::db();
		$attachments = $db->Execute('SELECT * FROM ConversationMessageAttachments WHERE cnvMessageID = ?', array(
			$cnvMessageID
		));
		return $attachments;
	}

	public function getAttachmentByID($cnvMessageAttachmentID) {
		$db = Loader::db();
		$attachment = $db->Execute('SELECT * FROM ConversationMessageAttachments WHERE cnvMessageAttachmentID = ?', array(
		$cnvMessageAttachmentID
		));
		return $attachment;
	}

	public static function add($cnv, $cnvMessageSubject, $cnvMessageBody, $parentMessage = false, $user = false) {
		$db = Loader::db();
		$date = Loader::helper('date')->getSystemDateTime();
		$uID = 0;

		if (is_object($user)) {
			$ux = $user;
		} else {
			$ux = new User();
		}

		if ($ux->isRegistered()) {
			$uID = $ux->getUserID();
		}
		$cnvMessageParentID = 0;
		$cnvMessageLevel = 0;
		if (is_object($parentMessage)) {
			$cnvMessageParentID = $parentMessage->getConversationMessageID();
			$cnvMessageLevel = $parentMessage->getConversationMessageLevel() + 1;
		}

		$cnvID = 0;
		if ($cnv instanceof Conversation) {
			$cnvID = $cnv->getConversationID();
		}

		$r = $db->Execute('insert into ConversationMessages (cnvMessageSubject, cnvMessageBody, cnvMessageDateCreated, cnvMessageParentID, cnvMessageLevel, cnvID, uID, cnvMessageSubmitIP, cnvMessageSubmitUserAgent) values (?, ?, ?, ?, ?, ?, ?, ?, ?)',
						  array($cnvMessageSubject, $cnvMessageBody, $date, $cnvMessageParentID, $cnvMessageLevel, $cnvID, $uID, ip2long(Loader::Helper('validation/ip')->getRequestIP()), $_SERVER['HTTP_USER_AGENT']));
		return ConversationMessage::getByID($db->Insert_ID());
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set uID = ?, cnvMessageSubject = null, cnvMessageBody = null, cnvIsMessageDeleted = 1 where cnvMessageID = ?', array(
			USER_DELETED_CONVERSATION_ID,
			$this->cnvMessageID
		));

		$this->cnvIsMessageDeleted = true;
		$this->cnvMessageSubject = null;
		$this->cnvMessageBody = null;
		$this->uID = USER_DELETED_CONVERSATION_ID;
	}

}
