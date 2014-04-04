<?
namespace Concrete\Block\FlashContent;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

	protected $btInterfaceWidth = 380;
	protected $btInterfaceHeight = 200;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockRecord = true;
	protected $btWrapperClass = 'ccm-ui';
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	protected $btTable = 'btFlashContent';
	protected $btExportFileColumns = array('fID');

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Embeds SWF files, including flash detection.");
	}
	
	public function getBlockTypeName() {
		return t("Flash Content");
	}

	public function getJavaScriptStrings() {
		return array('file-required' => t('You must select a file.'));	
	}
	
	function getFileID() {return $this->fID;}
	function getFileObject() {
		return File::getByID($this->fID);
	}		
	function getLinkText() {return $this->fileLinkText;}
	
	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('swfobject');
	}
}
?>