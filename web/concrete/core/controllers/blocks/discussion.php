<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the discussion block. This block is used to add a discussion (which is many conversations) to a website.
 *
 * @package Blocks
 * @subpackage Conversation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_Discussion extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btDiscussion';

		public function getBlockTypeDescription() {
			return t("Places a discussion a page.");
		}
		
		public function getBlockTypeName() {
			return t("Discussion");
		}

		public function on_page_view() {
			$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
			$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.conversations.css'));
			$this->addFooterItem(Loader::helper('html')->javascript('ccm.conversations.js'));
			$editor = ConversationEditor::getActive();
			foreach((array)$editor->getConversationEditorHeaderItems() as $item) {
				$this->addFooterItem($item);
			}
			$this->addFooterItem(Loader::helper('html')->javascript('dropzone.js'));

		}

		public function validate($post) {
			$e = Loader::helper('validation/error');
			if ($post['enableNewConversations']) {
				if ($post['ctID'] == '-1') {
					$e->add(t('You must choose a page type with a Main area.'));
				}
			}
			return $e;
		}
	}