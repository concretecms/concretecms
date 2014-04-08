<?
namespace Concrete\Block\Youtube;
use Loader;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {
	protected $btTable = 'btYouTube';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "210";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = false;

	public $title = '';
	public $videoURL = "";
	public $vHeight = "344";
	public $vWidth = "425";
	public $vPlayer ='1';
	public $mode = "youtube";
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Embeds a YouTube Video in your web page.");
	}
	
	public function getBlockTypeName() {
		return t("YouTube Video");
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);	
		if(!$this->title) $this->title=t("My Video");
	}
	
	function view(){ 
		$this->requireAsset('swfobject');
		$this->set('bID', $this->bID);	
		$this->set('title', $this->title);
		$this->set('videoURL', $this->videoURL);
		$this->set('vHeight', $this->vHeight);
		$this->set('vWidth', $this->vWidth);
		$this->set('vPlayer', $this->vPlayer);
		$this->set('mode', $this->mode);
	}
	
	function save($data) { 
		$args['title'] = isset($data['title']) ? trim($data['title']) : '';
		$args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';
		$args['vHeight'] = isset($data['vHeight']) ? trim($data['vHeight']) : '';
		$args['vWidth'] = isset($data['vWidth']) ? trim($data['vWidth']) : '';
		$args['vPlayer'] = ($data['vPlayer']==1) ? 1 : 0;
		parent::save($args);
	}
	
}

?>
