<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the conversation block. This block is used to display conversations in a page.
 *
 * @package Blocks
 * @subpackage Core Scrapbook/Clipboard Display
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_Conversation extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btConversation';

		public function getBlockTypeDescription() {
			return t("Displays conversations on a page.");
		}
		
		public function getBlockTypeName() {
			return t("Conversation");
		}

		public function save($post) {
			if (!$this->cvnID) {
				$conversation = Conversation::add();
			} 
			$values = array('cvID' => $conversation->getConversationID());
			parent::save($values);
		}
		
	}