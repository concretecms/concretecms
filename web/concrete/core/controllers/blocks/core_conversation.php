<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the conversation block. This block is used to display conversations in a page.
 *
 * @package Blocks
 * @subpackage Conversation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_CoreConversation extends BlockController implements ConversationFeatureInterface {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreConversation';
		protected $conversation;
		protected $btWrapperClass = 'ccm-ui';
		protected $btCopyWhenPropagate = true;
		protected $btFeatures = array(
			'conversation'
		);
		
		public function getBlockTypeDescription() {
			return t("Displays conversations on a page.");
		}
		
		public function getBlockTypeName() {
			return t("Conversation");
		}

		public function getConversationFeatureDetailConversationObject() {
			return $this->getConversationObject();
		}
		
		public function getConversationObject() {
			if (!isset($this->conversation)) {
				// i don't know why this->cnvid isn't sticky in some cases, leading us to query
				// every damn time
				$db = Loader::db();
				$cnvID = $db->GetOne('select cnvID from btCoreConversation where bID = ?', array($this->bID));
				$this->conversation = Conversation::getByID($cnvID);
			}
			return $this->conversation;
		}

		public function duplicate_master($newBID, $newPage) {
			parent::duplicate($newBID);
			$db = Loader::db();
			$conv = Conversation::add();
			$conv->setConversationPageObject($newPage);
			$this->conversation = $conv;
			$db->Execute('update btCoreConversation set cnvID = ? where bID = ?', array($conv->getConversationID(), $newBID));
		}

		public function view() {
			$req = Request::get();
			$req->requireAsset('core/conversation');
			$conversation = $this->getConversationObject();
			if (is_object($conversation)) {
				$this->set('conversation', $conversation);
				if ($this->enablePosting) {
					$token = Loader::helper('validation/token')->generate('add_conversation_message');
				} else {
					$token = '';
				}
				$this->set('posttoken', $token);
				$this->set('cID',Page::getCurrentPage()->getCollectionID());
				$this->set('users', $this->getActiveUsers(true));
			}
		}

		public function getActiveUsers($lower=false) {
			$cnv = $this->getConversationObject();
			$uobs = $cnv->getConversationMessageUsers();
			$users = array();
			foreach ($uobs as $user) {
				if ($lower) {
					$users[] = strtolower($user->getUserName());
				} else {
					$users[] = $user->getUserName();
				}
			}
			return $users;
		}

		public function save($post) {
			$db = Loader::db();
			$cnvID = $db->GetOne('select cnvID from btCoreConversation where bID = ?', array($this->bID));
			if (!$cnvID) {
				$conversation = Conversation::add();
				$b = $this->getBlockObject();
				$xc = $b->getBlockCollectionObject();
				$conversation->setConversationPageObject($xc);
			} else {
				$conversation = Conversation::getByID($cnvID);
			}
			$values = $post;
			if (!$values['itemsPerPage']) {
				$values['itemsPerPage'] = 0;
			}
			if (!$values['maxFilesGuest']) {
				$values['maxFilesGuest'] = 0;
			}
			if (!$values['maxFilesRegistered']) {
				$values['maxFilesRegistered'] = 0;
			}
			if (!$values['maxFileSizeGuest']) {
				$values['maxFileSizeGuest'] = 0;
			}
			if (!$values['maxFileSizeRegistered']) {
				$values['maxFileSizeRegistered'] = 0;
			}
			if (!$values['enableOrdering']) {
				$values['enableOrdering'] = 0;
			}
			if (!$values['enableCommentRating']) {
				$values['enableCommentRating'] = 0;
			}
			
			$values['fileExtensions'] = str_replace(' ', '', strtolower($values['fileExtensions']));
			
			$values['cnvID'] = $conversation->getConversationID();
			parent::save($values);
		}
		
	}