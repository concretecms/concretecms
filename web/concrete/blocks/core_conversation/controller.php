<?
namespace Concrete\Block\CoreConversation;
use Loader;
use \Concrete\Core\Block\BlockController;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Feature\ConversationFeatureInterface;
use Concrete\Core\Http\ResponseAssetGroup;
use Config;
use Page;

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
	class Controller extends BlockController implements ConversationFeatureInterface {

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

        public function edit(){
            $fileSettings = $this->getFileSettings();
            $this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
            $this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
            $this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
            $this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
            $this->set('fileExtensions', $fileSettings['fileExtensions']);
            $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled'] > 0 ? $fileSettings['attachmentsEnabled'] : '');
            $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled'] > 0 ? $fileSettings['attachmentOverridesEnabled'] : '');
        }

		public function view() {
			$r = ResponseAssetGroup::get();
			$r->requireAsset('core/conversation');
            $r->requireAsset('core/lightbox');
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
                $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled']);
                $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled']);
			}
		}

		public function getFileSettings(){
            $conversation = $this->getConversationObject();
			$helperFile = Loader::helper('concrete/file');
			if($conversation->getConversationMaxFilesGuest() > 0 && $conversation->getConversationAttachmentOverridesEnabled()  > 0) {
				$maxFilesGuest = $conversation->getConversationMaxFilesGuest();
			} else {
				$maxFilesGuest = Config::get('conversations.files.guest.max', 3);
			}
            if($conversation->getConversationAttachmentOverridesEnabled() > 0) {

                $attachmentOverridesEnabled = $conversation->getConversationAttachmentOverridesEnabled();
            }

			if($conversation->getConversationMaxFilesRegistered() > 0 && $conversation->getConversationAttachmentOverridesEnabled()  > 0) {
				$maxFilesRegistered = $conversation->getConversationMaxFilesRegistered();
			} else {
				$maxFilesRegistered = Config::get('conversations.files.registered.max', 6);
			}

			if($conversation->getConversationMaxFileSizeGuest() > 0 && $conversation->getConversationAttachmentOverridesEnabled()  > 0) {
				$maxFileSizeGuest = $conversation->getConversationMaxFileSizeGuest();
			} else {
				$maxFileSizeGuest = Config::get('conversations.files.guest.max_size', 3);
			}

			if($conversation->getConversationMaxFileSizeRegistered() > 0 && $conversation->getConversationAttachmentOverridesEnabled()  > 0) {
				$maxFileSizeRegistered = $conversation->getConversationMaxFileSizeRegistered();
			} else {
				$maxFileSizeRegistered = Config::get('conversations.files.registered.max_size', 10);
			}

			if($conversation->getConversationFileExtensions() && $conversation->getConversationAttachmentOverridesEnabled()  > 0) {
				$fileExtensions = $conversation->getConversationFileExtensions();
			} else {
				$fileExtensions = Config::get('conversations.files.allowed_types', '*.jpg;*.png;*.gif;*.doc');
			}

            if($conversation->getConversationAttachmentOverridesEnabled() > 0) {
                $attachmentsEnabled = $conversation->getConversationAttachmentsEnabled();
            } else {
                $attachmentsEnabled = is_numeric(Config::get('concrete.conversations.attachments_enabled')) ? Config::get('concrete.conversations.attachments_enabled') : 1;
            }

			$fileExtensions = implode(',', $helperFile->unserializeUploadFileExtensions($fileExtensions)); //unserialize and implode extensions into comma separated string

			$fileSettings = array();
			$fileSettings['maxFileSizeRegistered'] = $maxFileSizeRegistered;
			$fileSettings['maxFileSizeGuest'] = $maxFileSizeGuest;
			$fileSettings['maxFilesGuest'] = $maxFilesGuest;
			$fileSettings['maxFilesRegistered'] = $maxFilesRegistered;
			$fileSettings['fileExtensions'] = $fileExtensions;
            $fileSettings['attachmentsEnabled'] = $attachmentsEnabled;
            $fileSettings['attachmentOverridesEnabled'] = $attachmentOverridesEnabled;

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
            if ($values['attachmentOverridesEnabled']) {
                $conversation->setConversationAttachmentOverridesEnabled(intval($values['attachmentOverridesEnabled']));
            } else {
                $conversation->setConversationAttachmentOverridesEnabled(0);
            }
            if ($values['attachmentsEnabled']) {
                $conversation->setConversationAttachmentsEnabled(intval($values['attachmentsEnabled']));
            }
			if (!$values['itemsPerPage']) {
				$values['itemsPerPage'] = 0;
			}
			if ($values['maxFilesGuest']) {
				$conversation->setConversationMaxFilesGuest(intval($values['maxFilesGuest']));
			}
			if ($values['maxFilesRegistered']) {
                $conversation->setConversationMaxFilesRegistered(intval($values['maxFilesRegistered']));
			}
			if ($values['maxFileSizeGuest']) {
                $conversation->setConversationMaxFileSizeGuest(intval($values['maxFileSizeGuest']));
			}
			if ($values['maxFileSizeRegistered']) {
                $conversation->setConversationMaxFilesRegistered(intval($values['maxFileSizeRegistered']));
			}
			if (!$values['enableOrdering']) {
				$values['enableOrdering'] = 0;
			}
            if (!$values['attachmentsEnabled']) {
                $conversation->setConversationAttachmentsEnabled(intval($values['attachmentsEnabled']));
            }
			if (!$values['enableCommentRating']) {
				$values['enableCommentRating'] = 0;
			}

			if ($values['fileExtensions']) {
				$receivedExtensions = preg_split('{,}',strtolower($values['fileExtensions']),null,PREG_SPLIT_NO_EMPTY);
				$conversation->setConversationFileExtensions($helperFile->serializeUploadFileExtensions($receivedExtensions));
			}

			$values['cnvID'] = $conversation->getConversationID();
			parent::save($values);
		}

	}
