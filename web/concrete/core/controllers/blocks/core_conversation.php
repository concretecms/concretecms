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

		public function on_page_view() {
			$bt = BlockType::getByHandle('core_conversation');
			$conversation = $this->getConversationObject();
			if (is_object($conversation)) {
				$this->addHeaderItem(Loader::helper('html')->css('ccm.conversations.css'));
				$this->addHeaderItem(Loader::helper('html')->javascript('ccm.conversations.js'));
			}
			$editor = ConversationEditor::getActive();
			foreach((array)$editor->getConversationEditorHeaderItems() as $item) {
				$this->addHeaderItem($item);
			}
		}

		public function view() {
			$conversation = $this->getConversationObject();
			if (is_object($conversation)) {
				$this->set('conversation', $conversation);
				if ($this->enablePosting) {
					$token = Loader::helper('validation/token')->generate('add_conversation_message');
				} else {
					$token = '';
				}
				$this->set('posttoken', $token);
			}
		}

		public function save($post) {
			$db = Loader::db();
			$cnvID = $db->GetOne('select cnvID from btCoreConversation where bID = ?', array($this->bID));
			if (!$cnvID) {
				$conversation = Conversation::add();
			} else {
				$conversation = Conversation::getByID($cnvID);
			}
			$values = $post;
			if (!$values['itemsPerPage']) {
				$values['itemsPerPage'] = 0;
			}
			if (!$values['enableOrdering']) {
				$values['enableOrdering'] = 0;
			}
			if (!$values['enableCommentRating']) {
				$values['enableCommentRating'] = 0;
			}
			if (!$values['displayPostingForm']) {
				$values['displayPostingForm'] = 0;
			}
			if (!$values['insertNewMessages']) {
				$values['insertNewMessages'] = 0;
			}
			$values['cnvID'] = $conversation->getConversationID();
			parent::save($values);
		}
		
	}