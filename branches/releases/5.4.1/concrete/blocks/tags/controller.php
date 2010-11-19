<?php  defined('C5_EXECUTE') or die("Access Denied.");
	
class TagsBlockController extends BlockController {

	protected $btTable = 'btTags';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "350";
	
	protected $btCacheBlockOutput = false;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = false;
	public $attributeHandle = 'tags';
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	*/
	public function getBlockTypeDescription() {
		return t("List pages based on type, area.");
	}
	
	public function getBlockTypeName() {
		return t("Tags");
	}
	
	public function add() { 
		$this->loadAttribute();
	}
	
	protected function loadAttribute() {
		Loader::model('attribute/categories/collection');
		$ak = CollectionAttributeKey::getByHandle($this->attributeHandle);
		$this->set('ak',$ak);
	}
	
	public function edit() { 
		$this->loadAttribute();
	}
	
	public function view() {
		$this->loadAttribute();	
	}
	
	public function save($args) {
		$this->loadAttribute();
		$c = Page::getByID($_REQUEST['cID'], 'RECENT');
		$nvc = $c->getVersionToModify();
		$ak = $this->get('ak');
		$ak->saveAttributeForm($nvc);
		$nvc->refreshCache();
		parent::save($args);
	}	
}