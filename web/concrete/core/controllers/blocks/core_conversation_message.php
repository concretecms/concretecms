<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * The controller for the conversation message block. This block is used to display conversation messages in a page.
 *
 * @package Blocks
 * @subpackage Conversation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Concrete5_Controller_Block_CoreConversationMessage extends BlockController {

		protected $btCacheBlockRecord = true;
		protected $btTable = 'btCoreConversationMessage';

		public function getBlockTypeDescription() {
			return t("Places a conversation message into a page.");
		}
		
		public function getBlockTypeName() {
			return t("Conversation Message");
		}


		
	}