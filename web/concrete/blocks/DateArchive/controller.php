<?php 
namespace Concrete\Block\DateArchive;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

	protected $btTable = 'btDateArchive';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "350";

	protected $btExportPageColumns = array('targetCID');
	protected $btCacheBlockRecord = true;
		
	public $helpers =  array('navigation');	
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	*/
	public function getBlockTypeDescription() {
		return t("Displays month archive for pages");
	}
	
	public function getBlockTypeName() {
		return t("Blog Date Archive");
	}

	public function getJavaScriptStrings() {
		return array(
			'num-months-missing' => t('Please enter the number of months you want to show.')
		);
	}
		
	public function view() {
		if($this->targetCID > 0) {
			$target = Page::getByID($this->targetCID);
			$this->set('target',$target);
		}		
		
		$query = "SELECT MIN(cv.cvDatePublic) as firstPost 
			FROM CollectionVersions cv inner join Pages on cv.cID = Pages.cID
			INNER JOIN PageTypes pt ON Pages.ptID = pt.ptID
			WHERE pt.ptHandle IN ('blog_entry') and cIsTemplate = 0 and cvIsApproved = 1 and cIsActive = 1";
		$db = Loader::db();
		$firstPost = $db->getOne($query);

		if(strlen($firstPost)) {
			$firstPost = new DateTime($firstPost);
			$this->set('firstPost',$firstPost);
		}
	}
	
	public function save($args) {
		parent::save($args);
	}	
}