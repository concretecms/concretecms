<?
namespace Concrete\Block\Video;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

	protected $btInterfaceWidth = 320;
	protected $btInterfaceHeight = 220;
	protected $btTable = 'btVideo';
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	protected $btExportFileColumns = array('fID');
	protected $btWrapperClass = 'ccm-ui';
	
	public $width  = '';
	public $height = '';
	public $fID = 0;
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Embeds uploaded video into a web page. Supports AVI, WMV, Quicktime/MPEG4 and FLV formats.");
	}
	
	public function getBlockTypeName() {
		return t("Video Player");
	}

	public function getJavaScriptStrings() {
		return array('flv-required' => t('You must select a valid FLV file.'));
	}

	function getFileID() {return $this->fID;}
	function getFileObject() {
		return File::getByID($this->fID);
	}

	function save($data) { 
		$args['fID']    = intval($data['fID']);	
		$args['width']  = (intval($data['width'])>0)  ? intval($data['width'])  : 425;
		$args['height'] = (intval($data['height'])>0) ? intval($data['height']) : 334;		
		
		parent::save($args);
	}				

	public function view() {
		$this->requireAsset('swfobject');
	}
	
	
}

?>
