<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_AreaLayoutPreset extends Object {

	public static function add(AreaLayout $arLayout, $name) {
		$db = Loader::db();
		$db->Execute('insert into AreaLayoutPresets (arLayoutID, arLayoutPresetName) values (?, ?)', array(
			$arLayout->getAreaLayoutID(), $name
		));
		return AreaLayoutPreset::getByID($db->Insert_ID());
	}

	public static function getList() {
		$db = Loader::db();
		$r = $db->Execute('select arLayoutPresetID from AreaLayoutPresets order by arLayoutPresetName asc');
		$presets = array();
		while ($row = $r->FetchRow()) {
			$preset = AreaLayoutPreset::getByID($row['arLayoutPresetID']);
			if (is_object($preset)) {
				$presets[] = $preset;
			}
		}
		return $presets;
	}

	public static function getByID($arLayoutPresetID) {
		$db = Loader::db();
		$row = $db->GetRow('select arLayoutID, arLayoutPresetID, arLayoutPresetName from AreaLayoutPresets where arLayoutPresetID = ?', array(
			$arLayoutPresetID
		));
		if (is_array($row) && $row['arLayoutPresetID']) {
			$preset = new AreaLayoutPreset();
			$preset->setPropertiesFromArray($row);
			return $preset;
		}
	}

	public function getAreaLayoutPresetID() {return $this->arLayoutPresetID;}
	public function getAreaLayoutPresetName() {return $this->arLayoutPresetName;}
	public function getAreaLayoutID() {return $this->arLayoutID;}
	public function getAreaLayoutObject() {
		return AreaLayout::getByID($this->arLayoutID);
	}

}