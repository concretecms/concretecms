<?php
namespace Concrete\Block\File;
use Loader;
use File;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {
	protected $btInterfaceWidth = 300;
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = true;
	protected $btInterfaceHeight = 250;
	protected $btTable = 'btContentFile';
	
	protected $btExportFileColumns = array('fID');
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Link to files stored in the asset library.");
	}
	
	public function getBlockTypeName() {
		return t("File");
	}

	public function getJavaScriptStrings() {
		return array('file-required' => t('You must select a file.'));	
	}
	
	public function getSearchableContent(){
		return $this->fileLinkText;
	}
	
	public function validate($args) {
		$e = Loader::helper('validation/error');
		if ($args['fID'] < 1) {
			$e->add(t('You must select a file.'));
		}
		if (trim($args['fileLinkText']) == '') {
			$e->add(t('You must give your file a link.'));
		}
		return $e;
	}
	
	function getFileID() {return $this->fID;}
	
	function getFileObject() {
		return File::getByID($this->fID);
	}
	
	function getLinkText() {
		if ($this->fileLinkText) {
			return $this->fileLinkText;
		} else {
			$f = $this->getFileObject();
			return $f->getTitle();
		}
	}
	
	
	
}
?>
