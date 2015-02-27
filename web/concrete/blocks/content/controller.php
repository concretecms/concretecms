<?php
namespace Concrete\Block\Content;
use \Concrete\Core\Block\BlockController;
use \Concrete\Core\Editor\LinkAbstractor;

/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class Controller extends BlockController {

		protected $btTable = 'btContentLocal';
		protected $btInterfaceWidth = "600";
		protected $btInterfaceHeight = "465";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btSupportsInlineEdit = true;
		protected $btSupportsInlineAdd = true;
		protected $btCacheBlockOutputForRegisteredUsers = false;
		protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

		public function getBlockTypeDescription() {
			return t("HTML/WYSIWYG Editor Content.");
		}

		public function getBlockTypeName() {
			return t("Content");
		}

		function getContent() {
			return LinkAbstractor::translateFrom($this->content);
		}

		public function getSearchableContent(){
			return $this->content;
		}

		function br2nl($str) {
			$str = str_replace("\r\n", "\n", $str);
			$str = str_replace("<br />\n", "\n", $str);
			return $str;
		}

        public function registerViewAssets($outputContent)
        {
            if (preg_match('/data-concrete5-link-launch/i', $outputContent)) {
                $this->requireAsset('core/lightbox');
            }
        }

        public function view()
        {
            $this->set('content', $this->getContent());
        }

		function getContentEditMode() {
			return LinkAbstractor::translateFromEditMode($this->content);
		}

		public function add() {
			$this->requireAsset('redactor');
            $this->requireAsset('core/file-manager');
		}

		public function edit() {
			$this->requireAsset('redactor');
            $this->requireAsset('core/file-manager');
		}

		public function composer() {
			$this->requireAsset('redactor');
            $this->requireAsset('core/file-manager');
		}

		public function getImportData($blockNode) {
			$content = $blockNode->data->record->content;
			$content = LinkAbstractor::import($content);
			$args = array('content' => $content);
			return $args;
		}

		public function export(\SimpleXMLElement $blockNode) {
			$data = $blockNode->addChild('data');
			$data->addAttribute('table', $this->btTable);
			$record = $data->addChild('record');
			$cnode = $record->addChild('content');
			$node = dom_import_simplexml($cnode);
			$no = $node->ownerDocument;
			$content = LinkAbstractor::export($this->content);
			$cdata = $no->createCDataSection($content);
			$node->appendChild($cdata);
		}

		function save($args) {
			$args['content'] = LinkAbstractor::translateTo($args['content']);
			parent::save($args);
		}

	}

?>
