<?
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ContentBlockController extends BlockController {
		
		var $pobj;
		
		protected $btTable = 'btContentLocal';
		protected $btInterfaceWidth = "600";
		protected $btInterfaceHeight = "465";
		
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
		
		function br2nl($str) {
			$str = str_replace("\r\n", "\n", $str);
			$str = str_replace("<br />\n", "\n", $str);
			return $str;
		}
		
		function getContentEditMode() {
			$content = $this->translateFrom($this->content);
			return $content;				
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
			
			// now support for files
			
			
			return $text;
		}
		
		private function replaceCollectionID($match) {
			$cID = $match[1];
			if ($cID > 0) {
				$path = Page::getCollectionPathFromID($cID);
				return $path;
			}
		}
	
		function translateTo($text) {
			// keep links valid
			$url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
			$url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
			$text = preg_replace(
				array(
					'/' . $url1 . '\?cID=([0-9]+)/i', 
					'/' . $url2 . '/i'),
				array(
					'{CCM:CID_\\1}',
					'{CCM:BASE_URL}')
				, $text);
			return $text;
		}
		
		function save($data) {
			$content = $this->translateTo($data['content']);
			$args['content'] = $content;
			parent::save($args);
		}
		
	}
	
?>