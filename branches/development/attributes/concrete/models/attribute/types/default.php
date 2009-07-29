<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class DefaultAttributeTypeController extends AttributeTypeController  {


	public function getValue($avID) {
		$db = Loader::db();
		$value = $db->GetOne("select value from atDefault where avID = ?", array($avID));
		return $value;	
	}

	public function form($attributeKey, $attributeValue) {
		print '<textarea name="' . $this->field('value') . '" style="width: 100%; height: 40px">' . $attributeValue . '</textarea>';
	}

	public function setValue() {
	
	}
	
	public function save($avID, $data) {
			
	}
}
