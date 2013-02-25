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
	class Concrete5_Controller_Block_CoreConversation extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreConversation';
		protected $conversation;
		protected $btWrapperClass = 'ccm-ui';

		public function getBlockTypeDescription() {
			return t("Displays conversations on a page.");
		}
		
		public function getBlockTypeName() {
			return t("Conversation");
		}

		protected function getConversationObject() {
			if (!isset($this->conversation)) {
				if ($this->cnvID) {
					$this->conversation = Conversation::getByID($this->cnvID);
				}
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
			$values['cnvID'] = $conversation->getConversationID();
			parent::save($values);
		}
		
	}