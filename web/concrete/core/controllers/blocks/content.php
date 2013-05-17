<?
	defined('C5_EXECUTE') or die("Access Denied.");
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
	class Concrete5_Controller_Block_Content extends BlockController {
		
		protected $btTable = 'btContentLocal';
		protected $btInterfaceWidth = "600";
		protected $btInterfaceHeight = "465";
		protected $btCacheBlockRecord = true;
		protected $btCacheBlockOutput = true;
		protected $btCacheBlockOutputOnPost = true;
		protected $btCacheBlockOutputForRegisteredUsers = true;
		protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared
		
		public function getBlockTypeDescription() {
			return t("HTML/WYSIWYG Editor Content.");
		}
		
		public function getBlockTypeName() {
			return t("Content");
		}
		
		function getContent() {
			$content = $this->translateFrom($this->content);
			return $content;				
		}
		
		public function getSearchableContent(){
			return $this->content;
		}
		
		function br2nl($str) {
			$str = str_replace("\r\n", "\n", $str);
			$str = str_replace("<br />\n", "\n", $str);
			return $str;
		}
		
		function getContentEditMode() {
			$content = $this->translateFromEditMode($this->content);
			return $content;				
		}

		public function getImportData($blockNode) {
			$args = array();
			$content = $blockNode->data->record->content;

			$content = preg_replace_callback(
				'/\{ccm:export:page:(.*)\}/i',
				array('ContentBlockController', 'replacePagePlaceHolderOnImport'),				
				$content);

			$content = preg_replace_callback(
				'/\{ccm:export:image:(.*)\}/i',
				array('ContentBlockController', 'replaceImagePlaceHolderOnImport'),				
				$content);

			$content = preg_replace_callback(
				'/\{ccm:export:file:(.*)\}/i',
				array('ContentBlockController', 'replaceFilePlaceHolderOnImport'),				
				$content);

			$content = preg_replace_callback(
				'/\{ccm:export:define:(.*)\}/i',
				array('ContentBlockController', 'replaceDefineOnImport'),				
				$content);

			$args['content'] = $content;			
			return $args;
		}
		
		public static function replacePagePlaceHolderOnImport($match) {
			$cPath = $match[1];
			if ($cPath) { 
				$pc = Page::getByPath($cPath);
				return '{CCM:CID_' . $pc->getCollectionID() . '}';
			} else {
				return '{CCM:CID_1}';
			}
		}

		public static function replaceDefineOnImport($match) {
			$define = $match[1];
			if (defined($define)) {
				$r = get_defined_constants();
				return $r[$define];
			}
		}

		public static function replaceImagePlaceHolderOnImport($match) {
			$filename = $match[1];
			$db = Loader::db();
			$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
			return '{CCM:FID_' . $fID . '}';
		}
		
		public static function replaceFilePlaceHolderOnImport($match) {
			$filename = $match[1];
			$db = Loader::db();
			$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
			return '{CCM:FID_DL_' . $fID . '}';
		}
		
		public function export(SimpleXMLElement $blockNode) {			
			
			$data = $blockNode->addChild('data');
			$data->addAttribute('table', $this->btTable);
			$record = $data->addChild('record');
			$content = $this->content;
			$content = preg_replace_callback(
				'/{CCM:CID_([0-9]+)}/i',
				array('ContentExporter', 'replacePageWithPlaceHolderInMatch'),				
				$content);

			$content = preg_replace_callback(
				'/{CCM:FID_([0-9]+)}/i',
				array('ContentExporter', 'replaceImageWithPlaceHolderInMatch'),				
				$content);

			$content = preg_replace_callback(
				'/{CCM:FID_DL_([0-9]+)}/i',
				array('ContentExporter', 'replaceFileWithPlaceHolderInMatch'),				
				$content);


			$cnode = $record->addChild('content');
			$node = dom_import_simplexml($cnode);
			$no = $node->ownerDocument;
			$node->appendChild($no->createCDataSection($content));

		}


		function translateFromEditMode($text) {
			// now we add in support for the links
			
			$text = preg_replace(
				'/{CCM:CID_([0-9]+)}/i',
				BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=\\1',
				$text);

			// now we add in support for the files
			
			$text = preg_replace_callback(
				'/{CCM:FID_([0-9]+)}/i',
				array('ContentBlockController', 'replaceFileIDInEditMode'),				
				$text);

			
			$text = preg_replace_callback(
				'/{CCM:FID_DL_([0-9]+)}/i',
				array('ContentBlockController', 'replaceDownloadFileIDInEditMode'),				
				$text);
			

			return $text;
		}
		
		function translateFrom($text) {
			// old stuff. Can remove in a later version.
			$text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
			$text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);

			// we have the second one below with the backslash due to a screwup in the
			// 5.1 release. Can remove in a later version.

			$text = preg_replace(
				array(
					'/{\[CCM:BASE_URL\]}/i',
					'/{CCM:BASE_URL}/i'),
				array(
					BASE_URL . DIR_REL,
					BASE_URL . DIR_REL)
				, $text);
				
			// now we add in support for the links
			
			$text = preg_replace_callback(
				'/{CCM:CID_([0-9]+)}/i',
				array('ContentBlockController', 'replaceCollectionID'),				
				$text);

			$text = preg_replace_callback(
				'/<img [^>]*src\s*=\s*"{CCM:FID_([0-9]+)}"[^>]*>/i',
				array('ContentBlockController', 'replaceImageID'),				
				$text);

			// now we add in support for the files that we view inline			
			$text = preg_replace_callback(
				'/{CCM:FID_([0-9]+)}/i',
				array('ContentBlockController', 'replaceFileID'),				
				$text);

			// now files we download
			
			$text = preg_replace_callback(
				'/{CCM:FID_DL_([0-9]+)}/i',
				array('ContentBlockController', 'replaceDownloadFileID'),				
				$text);
			
			return $text;
		}
		
		private function replaceFileID($match) {
			$fID = $match[1];
			if ($fID > 0) {
				$path = File::getRelativePathFromID($fID);
				return $path;
			}
		}
		
		private function replaceImageID($match) {
			$fID = $match[1];
			if ($fID > 0) {
				preg_match('/width\s*="([0-9]+)"/',$match[0],$matchWidth);
				preg_match('/height\s*="([0-9]+)"/',$match[0],$matchHeight);
				$file = File::getByID($fID);
				if (is_object($file) && (!$file->isError())) {
					$imgHelper = Loader::helper('image');
					$maxWidth = ($matchWidth[1]) ? $matchWidth[1] : $file->getAttribute('width');
					$maxHeight = ($matchHeight[1]) ? $matchHeight[1] : $file->getAttribute('height');
					if ($file->getAttribute('width') > $maxWidth || $file->getAttribute('height') > $maxHeight) {
						$thumb = $imgHelper->getThumbnail($file, $maxWidth, $maxHeight);
						return preg_replace('/{CCM:FID_([0-9]+)}/i', $thumb->src, $match[0]);
					}
				}
				return $match[0];
			}
		}

		private function replaceDownloadFileID($match) {
			$fID = $match[1];
			if ($fID > 0) {
				$c = Page::getCurrentPage();
				if (is_object($c)) {
					return View::url('/download_file', 'view', $fID, $c->getCollectionID());
				} else {
					return View::url('/download_file', 'view', $fID);
				}
			}
		}

		private function replaceDownloadFileIDInEditMode($match) {
			$fID = $match[1];
			if ($fID > 0) {
				return View::url('/download_file', 'view', $fID);
			}
		}
		
		private function replaceFileIDInEditMode($match) {
			$fID = $match[1];
			return View::url('/download_file', 'view_inline', $fID);
		}
		
		private function replaceCollectionID($match) {
			$cID = $match[1];
			if ($cID > 0) {
				$c = Page::getByID($cID, 'ACTIVE');
				return Loader::helper("navigation")->getLinkToCollection($c);
			}
		}
		
		function translateTo($text) {
			// keep links valid
			$url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
			$url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
			$url3 = View::url('/download_file', 'view_inline');
			$url3 = str_replace('/', '\/', $url3);
			$url3 = str_replace('-', '\-', $url3);
			$url4 = View::url('/download_file', 'view');
			$url4 = str_replace('/', '\/', $url4);
			$url4 = str_replace('-', '\-', $url4);
			$text = preg_replace(
				array(
					'/' . $url1 . '\?cID=([0-9]+)/i', 
					'/' . $url3 . '([0-9]+)\//i', 
					'/' . $url4 . '([0-9]+)\//i', 
					'/' . $url2 . '/i'),
				array(
					'{CCM:CID_\\1}',
					'{CCM:FID_\\1}',
					'{CCM:FID_DL_\\1}',
					'{CCM:BASE_URL}')
				, $text);
			return $text;
		}
		
		function save($args) {
			$args['content'] = $this->translateTo($args['content']);
			parent::save($args);
		}
		
	}
	
?>
