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

		$cnv = $this->getConversationObject();
		if (is_object($cnv)) {
			$cnv->updateConversationSummary();
		}

	}
	public function unapprove() {
		$db = Loader::db();
		$db->execute('UPDATE ConversationMessages SET cnvIsMessageApproved=0 WHERE cnvMessageID=?',array($this->cnvMessageID));
		$this->cnvIsMessageApproved = false;

		$cnv = $this->getConversationObject();
		if (is_object($cnv)) {
			$cnv->updateConversationSummary();
		}
	}

	public function conversationMessageHasFlag($flag) {
		if (!$flag instanceof ConversationFlagType) {
			$flag = ConversationFlagType::getByHandle($flag);
		}
		if ($flag instanceof ConversationFlagType) {
			foreach ($this->getConversationMessageFlagTypes() as $type) {
				if ($flag->getConversationFlagTypeID() == $type->getConversationFlagTypeID()) {
					return true;
				}
			}
		}
		return false;
	}
	public function getConversationMessageBodyOutput($dashboardOverride = false) {
		$editor = ConversationEditor::getActive();
		if($dashboardOverride) {
			return $this->cnvMessageBody;
		}
		else if ($this->cnvIsMessageDeleted) {
			return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message has been deleted.'));
			//return t('This message has been deleted.');
		} else if (!$this->cnvIsMessageApproved) {
			if ($this->conversationMessageHasFlag('spam')) {
				return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message has been flagged as spam.'));
				//return t('This message has been flagged as spam.');
			}
			return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message is queued for approval.'));
			// return t('This message is queued for approval.');
		} else {
			return $editor->formatConversationMessageBody($this->getConversationObject(),$this->cnvMessageBody);
		}
	}

	public function getConversationObject() {
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
	public function getConversationMessageDateTimeOutput($format = 'default') {
		if(is_array($format)) {  // custom date format
			return t('Posted on %s', Loader::helper('date')->date($format[0], strtotime($this->cnvMessageDateCreated)));
		}
		switch($format) {
			case 'elapsed':   // 3 seconds ago, 4 days ago, etc.
				$timestamp = strtotime($this->cnvMessageDateCreated);
				$time = array(
					12 * 30 * 24 * 60 * 60  => array(t('year'), t('years')),
	                30 * 24 * 60 * 60  => array(t('month'), t('months')),
	                24 * 60 * 60  => array(t('day'), t('days')),
	                60 * 60  => array(t('hour'), t('hours')),
	                60  => array(t('minute'), t('minutes')),
	                1   => array(t('second'), t('seconds'))                                   
                );
		                                                         
		        $ptime = time() - $timestamp;
		                        
				foreach ($time as $seconds => $str) {
			        $elp = $ptime / $seconds;
			        if($elp <= 0) {
			                return t('0 seconds ago');
			        }
			        if($elp >= 1) {
			        
			            $rounded = round($elp);
						if($rounded > 1)  {
							$str = $str[1]; // plural
			             } else {
			             	$str = $str[0]; // singular
			             }
						 
						 $ago = t('ago');

			            $elapsed =  sprintf('%s %s %s', $rounded, $str, $ago);
			            return $elapsed;
			        }
				}
			break;
			case 'mdy':
				return t('Posted on %s', Loader::helper('date')->date(DATE_APP_GENERIC_MDY, strtotime($this->cnvMessageDateCreated))); 
			case 'mdy_full':
				return t('Posted on %s', Loader::helper('date')->date(DATE_APP_GENERIC_MDY_FULL, strtotime($this->cnvMessageDateCreated))); 
			default:
				return t('Posted on %s', Loader::helper('date')->date(DATE_APP_GENERIC_MDY_FULL, strtotime($this->cnvMessageDateCreated))); 
			 	//return t('Posted on %s', Loader::helper('date')->date('F d, Y \a\t g:i a', strtotime($this->cnvMessageDateCreated)));
				break;
		}
	}
	public function rateMessage(ConversationRatingType $ratingType, $commentRatingIP, $commentRatingUserID, $post = array()) {
		$db = Loader::db();
		$cnvRatingTypeID = $db->GetOne('SELECT * FROM ConversationRatingTypes WHERE cnvRatingTypeHandle = ?', array($ratingType->cnvRatingTypeHandle));
		$db->Execute('INSERT INTO ConversationMessageRatings (cnvMessageID, cnvRatingTypeID, cnvMessageRatingIP, timestamp, uID) VALUES (?, ?, ?, ?, ?)', array($this->getConversationMessageID(), $cnvRatingTypeID, $commentRatingIP, date('Y-m-d H:i:s'), $commentRatingUserID));
		$ratingType->adjustConversationMessageRatingTotalScore($this);
	}	
	public function getConversationMessageRating(ConversationRatingType $ratingType) {
		$db = Loader::db();
		$cnt = $db->GetOne('SELECT count(*) from ConversationMessageRatings where cnvRatingTypeID = ? AND cnvMessageID = ?',  array($ratingType->getConversationRatingTypeID(), $this->cnvMessageID));
		return $cnt;
	}
	
	public function flag($flagtype) {
		if ($flagtype instanceof ConversationFlagType) {
			$db = Loader::db();
			foreach ($this->getConversationMessageFlagTypes() as $ft) {
				if ($ft->getConversationFlagTypeID() === $flagtype->getConversationFlagTypeID()) {
					return;
				}
			}
			$db->execute('INSERT INTO ConversationFlaggedMessages (cnvMessageFlagTypeID, cnvMessageID) VALUES (?,?)',array($flagtype->getConversationFlagTypeID(),$this->getConversationMessageID()));
			$this->cnvMessageFlagTypes[] = $flagtype;
			$this->unapprove();
			return true;
		}
		throw new Exception('Invalid flag type.');
	}

	public function unflag($flagtype) {
		if ($flagtype instanceof ConversationFlagType) {
			$db = Loader::db();
			$db->execute('DELETE FROM ConversationFlaggedMessages WHERE cnvMessageFlagTypeID = ? AND cnvMessageID = ?',array($flagtype->getConversationFlagTypeID(),$this->getConversationMessageID()));
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

		if ($cnv instanceof Conversation) {
			$cnv->updateConversationSummary();
		}

		return ConversationMessage::getByID($db->Insert_ID());
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set cnvIsMessageDeleted = 1, cnvIsMessageApproved = 0 where cnvMessageID = ?', array(
			$this->cnvMessageID
		));

		$cnv = $this->getConversationObject();
		if (is_object($cnv)) {
			$cnv->updateConversationSummary();
		}

		$this->cnvIsMessageDeleted = true;
		//$this->cnvMessageSubject = null;
		//$this->cnvMessageBody = null;
		// $this->uID = USER_DELETED_CONVERSATION_ID;
	}

	public function restore() {
		$db = Loader::db();
		$db->Execute('update ConversationMessages set cnvIsMessageDeleted = 0 where cnvMessageID = ?', array(
			$this->cnvMessageID
		));

		$cnv = $this->getConversationObject();
		if (is_object($cnv)) {
			$cnv->updateConversationSummary();
		}

		$this->cnvIsMessageDeleted = false;
		//$this->cnvMessageSubject = null;
		//$this->cnvMessageBody = null;
		// $this->uID = USER_DELETED_CONVERSATION_ID;
	}

}
