<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * A block controller for displaying HTML content on a page.
 *
 * @package Blocks
 * @subpackage HTML
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Controller_Block_Html extends BlockController {
	
	protected $btTable = 'btContentLocal';
	protected $btInterfaceWidth = "600";
	protected $btWrapperClass = 'ccm-ui';
	protected $btInterfaceHeight = "465";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = true;
	
	public $content = "";	
	
	public function getBlockTypeDescription() {
		return t("For adding HTML by hand.");
	}
	
	public function getBlockTypeName() {
		return t("HTML");
	}	 
	
	public function __construct($obj = null) {		
		parent::__construct($obj); 
	}
	
	public function view(){ 
		$this->set('content', $this->content); 
	} 
	
	public function getSearchableContent() {
		return $this->content;
	}
	
	public function save($data) { 
		$args['content'] = isset($data['content']) ? $data['content'] : '';
		parent::save($args);
	}
	
	public function xml_highlight($s){       
		$s = htmlspecialchars($s);
		$s = preg_replace("#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
			"<font color=\"#0000FF\">&lt;\\1\\2\\3&gt;</font>",$s);
		$s = preg_replace("#&lt;([\?])(.*)([\?])&gt;#sU",
			"<font color=\"#800000\">&lt;\\1\\2\\3&gt;</font>",$s);
		$s = preg_replace("#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
			"&lt;<font color=\"#808000\">\\1\\2</font>\\3",$s);
		$s = preg_replace("#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
			"&lt;\\1<font color=\"#808000\">\\2</font>\\3&gt;",$s);
		$s = preg_replace("#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
			"<font color=\"#800080\">\\1</font>=<font color=\"#FF00FF\">\\2\\3\\4</font>",$s);
		$s = preg_replace("#&lt;(.*)(\[)(.*)(\])&gt;#isU",
			"&lt;\\1<font color=\"#800080\">\\2\\3\\4</font>&gt;",$s);
		return nl2br($s);
	}
}

?>