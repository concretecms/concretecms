<?php defined('C5_EXECUTE') or die("Access Denied.");
	
class TagsBlockController extends BlockController {

	protected $btTable = 'btTags';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "350";
	
	// disable caching for development
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	*/
	public function getBlockTypeDescription() {
		return t("List pages based on type, area.");
	}
	
	public function getBlockTypeName() {
		return t("Tags");
	}
	
	protected function load() {
		parent::load();
		Loader::model('attribute/categories/collection');
		//$c = $this->getCollectionObject();
		$c = Page::getCurrentPage();
		$ak = CollectionAttributeKey::getByHandle($this->attributeHandle);
		$this->set('ak',$ak);
		$this->set('c',$c);
	} 
	
	public function add() {  }
	/*
	public function edit() {  }
	public function view() { }
	*/

	public function save($args) {
		$c = Page::getCurrentPage();
		$ak = CollectionAttributeKey::getByHandle($this->attributeHandle);
		//$c->saveAttribute();
		
		parent::save($args);
	}	
}
?>