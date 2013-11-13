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
			$r = ResponseAssetGroup::get();
			$r->requireAsset('core/conversation');
			$fileSettings = $this->getFileSettings(); 
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
				$this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
				$this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
				$this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
				$this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
				$this->set('fileExtensions', $fileSettings['fileExtensions']);
			}
		}
		
		public function getFileSettings(){
			$helperFile = Loader::helper('concrete/file');
			if($this->maxFilesGuest > 0) {
				$maxFilesGuest = $this->maxFilesGuest;
			} else {
				$maxFilesGuest = Config::get('CONVERSATIONS_MAX_FILES_GUEST') ? Config::get('CONVERSATIONS_MAX_FILES_GUEST') : 3;
			}
			
			if($this->maxFilesRegistered > 0) {
				$maxFilesRegistered = $this->maxFilesRegistered;
			} else {
				$maxFilesRegistered = Config::get('CONVERSATIONS_MAX_FILES_REGISTERED') ? Config::get('CONVERSATIONS_MAX_FILES_REGISTERED') : 6;
			}
			
			if($this->maxFileSizeGuest > 0) {
				$maxFileSizeGuest = $this->maxFileSizeGuest;
			} else {
				$maxFileSizeGuest = Config::get('CONVERSATIONS_MAX_FILE_SIZE_GUEST') ? Config::get('CONVERSATIONS_MAX_FILE_SIZE_GUEST') : 3;
			}
			
			if($this->maxFileSizeRegistered > 0) {
				$maxFileSizeRegistered = $this->maxFileSizeRegistered;
			} else {
				$maxFileSizeRegistered = Config::get('CONVERSATIONS_MAX_FILE_SIZE_REGISTERED') ? Config::get('CONVERSATIONS_MAX_FILE_SIZE_REGISTERED') : 10;
			}
			
			if($this->fileExtensions) {
				$fileExtensions = $this->fileExtensions;
			} else {
				$fileExtensions = Config::get('CONVERSATIONS_ALLOWED_FILE_TYPES') ? Config::get('CONVERSATIONS_ALLOWED_FILE_TYPES') : '*.jpg;*.png;*.gif;*.doc';
			}
			
			$fileExtensions = implode(',', $helperFile->unserializeUploadFileExtensions($fileExtensions)); //unserialize and implode extensions into comma separated string
			
			$fileSettings = array();
			$fileSettings['maxFileSizeRegistered'] = $maxFileSizeRegistered;
			$fileSettings['maxFileSizeGuest'] = $maxFileSizeGuest;
			$fileSettings['maxFilesGuest'] = $maxFilesGuest;
			$fileSettings['maxFilesRegistered'] = $maxFilesRegistered;
			$fileSettings['fileExtensions'] = $fileExtensions;
			
			return $fileSettings;
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
			$helperFile = Loader::helper('concrete/file');
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
			
			if ($values['fileExtensions']) {
				$receivedExtensions = preg_split('{,}',strtolower($values['fileExtensions']),null,PREG_SPLIT_NO_EMPTY);
				$values['fileExtensions'] = $helperFile->serializeUploadFileExtensions($receivedExtensions);
			}
			
			 
			$values['cnvID'] = $conversation->getConversationID();
			parent::save($values);
		}
		
	}