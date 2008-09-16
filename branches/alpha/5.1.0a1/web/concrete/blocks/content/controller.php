<?
	class ContentBlockController extends BlockController {
		
		protected $btDescription = "HTML/WYSIWYG Editor Content";
		protected $btName = "Content";
		protected $btTable = 'btContentLocal';
		protected $btInterfaceWidth = "600";
		protected $btInterfaceHeight = "465";
		
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
			// keep links valid
			$text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
			$text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);
			return $text;
		}
	
		function translateTo($text) {
			// keep links valid
			$text = str_replace('href="' . BASE_URL . DIR_REL, 'href="{[CCM:BASE_URL]}', $text);
			$text = str_replace('src="' . BASE_URL . REL_DIR_FILES_UPLOADED, 'src="{[CCM:REL_DIR_FILES_UPLOADED]}', $text);
			return $text;
		}
		
		function save($data) {
			$content = $this->translateTo($data['content']);
			$args['content'] = $content;
			parent::save($args);
		}
		
	}
	
?>