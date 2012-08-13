<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/profile/edit');
class Concrete5_Controller_Profile_Messages extends ProfileEditController {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function on_start() {
		parent::on_start();
		$this->error = Loader::helper('validation/error');
		$this->set('vt', Loader::helper('validation/token'));
		$this->set('text', Loader::helper('text'));
	}
	
	public function view() {
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		
		$inbox = UserPrivateMessageMailbox::get($ui, UserPrivateMessageMailbox::MBTYPE_INBOX);
		$sent = UserPrivateMessageMailbox::get($ui, UserPrivateMessageMailbox::MBTYPE_SENT);
		
		$this->set('inbox', $inbox);
		$this->set('sent', $sent);
	}
	
	protected function validateUser($uID) {
		if ($uID > 0) { 
			$ui = UserInfo::getByID($uID);
			if ((is_object($ui)) && ($ui->getAttribute('profile_private_messages_enabled') == 1)) {
				$this->set('recipient', $ui);
				return true;
			}
		}
		
		$this->redirect('/profile');
	}
	
	protected function getMessageMailboxID($box) {
		$msgMailboxID = 0;
		switch($box) {
			case 'inbox':
				$msgMailboxID = UserPrivateMessageMailbox::MBTYPE_INBOX;
				break;
			case 'sent':
				$msgMailboxID = UserPrivateMessageMailbox::MBTYPE_SENT;
				break;
			default:
				$msgMailboxID = $box;
				break;
		}
		return $msgMailboxID;	
	}
	
	public function view_mailbox($box) {
		$msgMailboxID = $this->getMessageMailboxID($box);
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		
		$mailbox = UserPrivateMessageMailbox::get($ui, $msgMailboxID);
		if (is_object($mailbox)) {
			$messageList = $mailbox->getMessageList();
			$messages = $messageList->getPage();
			$this->set('messages', $messages);
			$this->set('messageList', $messageList);
		}
		
		// also, we have to mark all messages in this mailbox as no longer "new"
		
		$mailbox->removeNewStatus();
		$this->set('mailbox', $box);
	}
	
	public function view_message($box, $msgID) {
		$msgMailboxID = $this->getMessageMailboxID($box);
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$mailbox = UserPrivateMessageMailbox::get($ui, $msgMailboxID);
		
		$msg = UserPrivateMessage::getByID($msgID, $mailbox);
		if ($ui->canReadPrivateMessage($msg)) {
			$msg->markAsRead();
			$this->set('subject', $msg->getFormattedMessageSubject());
			$this->set('msgContent', $msg->getMessageBody());
			$this->set('dateAdded', $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')));
			$this->set('author', $msg->getMessageAuthorObject());
			$this->set('msg', $msg);
			$this->set('box', $box);			
			$this->set('backURL', View::url('/profile/messages', 'view_mailbox', $box));
			$valt = Loader::helper('validation/token');
			$token = $valt->generate('delete_message_' . $msgID);
			$this->set('deleteURL', View::url('/profile/messages', 'delete_message', $box, $msgID, $token));
		}
	}
	
	public function delete_message($box, $msgID, $token) {
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_message_' . $msgID, $token)) {
			$this->error->add($valt->getErrorMessage());
		}
		
		$msgMailboxID = $this->getMessageMailboxID($box);
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$mailbox = UserPrivateMessageMailbox::get($ui, $msgMailboxID);
		
		$msg = UserPrivateMessage::getByID($msgID, $mailbox);
		if ($ui->canReadPrivateMessage($msg) && (!$this->error->has())) {
			$msg->delete();
			$this->redirect('/profile/messages', 'view_mailbox', $box);
		}
		print $this->view();
	}
	
	public function write($uID) {
		$this->validateUser($uID);
		$this->set('backURL', View::url('/profile', 'view', $uID));
	}

	public function reply($boxID, $msgID) {
		$msg = UserPrivateMessage::getByID($msgID);
		$uID = $msg->getMessageRelevantUserID();
		$this->validateUser($uID);
		$this->set('backURL', View::url('/profile/messages', 'view_message', $boxID, $msgID));
		$this->set('msgID', $msgID);
		$this->set('box', $boxID);
		$this->set('msg', $msg);
		
		$this->set('msgSubject', $msg->getFormattedMessageSubject());
		
		$body = "\n\n\n" . $msg->getMessageDelimiter() . "\n";
		$body .= t("From: %s\nDate Sent: %s\nSubject: %s", $msg->getMessageAuthorName(), $msg->getMessageDateAdded('user', t('F d, Y \a\t g:i A')), $msg->getFormattedMessageSubject());
		$body .= "\n\n" . $msg->getMessageBody();
		$this->set('msgBody', $body);
	}
	
	public function send() {
		$uID = $this->post('uID');
	
		if ($this->post('msgID') > 0) { 
			$msgID = $this->post('msgID');
			$box = $this->post('box');
			$this->reply($box, $msgID);
		} else {
			$this->write($uID);
		}
		
		$vf = Loader::helper('validation/form');
		$vf->setData($this->post());
		$vf->addRequired('msgBody', t("You haven't written a message!"));
		$vf->addRequiredToken("validate_send_message");
		if ($vf->test()) {
			$u = new User();
			$sender = UserInfo::getByID($u->getUserID());
			$r = $sender->sendPrivateMessage($this->get('recipient'), $this->post('msgSubject'), $this->post('msgBody'), $this->get('msg'));
			if ($r instanceof ValidationErrorHelper) {
				$this->error = $r;
			} else {
				if ($this->post('msgID') > 0) { 
					$this->redirect('/profile/messages', 'reply_complete', $box, $msgID);
				} else {
					$this->redirect('/profile/messages', 'send_complete', $uID);
				}
			}
		} else {
			$this->error = $vf->getError();
		}		
	}
	
	public function send_complete($uID) { 
		$this->validateUser($uID);
	}

	public function reply_complete($box, $msgID) { 
		$this->reply($box, $msgID);
	}
	
	public function on_before_render() {
		$this->set('error', $this->error);
	}

}