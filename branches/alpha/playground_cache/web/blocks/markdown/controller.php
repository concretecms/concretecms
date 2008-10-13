<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class MarkdownBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "Content added using the markdown syntax.";
		protected $btName = "Markdown Content";
		protected $btTable = 'btContentLocal';
		protected $btInterfaceWidth = "500";
		protected $btInterfaceHeight = "400";
		
		function getContent() {
			Loader::library('3rdparty/markdown');
			$content = Markdown($this->content);
			return $content;				
		}
		
		
	}
	
?>