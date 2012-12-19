<?

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_SubArea extends Area {

	protected $arIsSubArea = 1;
	protected $areaDelimiter = ':';

	public function __construct($arHandle, Area $parent) {
		$arHandle = $parent->getAreaHandle() . ' ' . $this->areaDelimiter . ' ' . $arHandle;
		$c = $parent->getAreaCollectionObject();
		parent::__construct($arHandle);
		$this->c = $c;
		$this->arParentID = $parent->getAreaID();
	}	


}