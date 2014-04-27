<?
namespace Concrete\Core\Page\Style;
use Loader;
use \Concrete\Core\Foundation\Object;
class CustomStylePreset extends Object {

	public function getList() {
		$db = Loader::db();
		$r = $db->Execute('select cspID, cspName, csrID from CustomStylePresets order by cspName asc');
		$presets = array();
		while ($row = $r->FetchRow()) {
			$obj = new CustomStylePreset();
			$obj->setPropertiesFromArray($row);
			$presets[] = $obj;
		}
		return $presets;
	}

	public function getCustomStylePresetID() {return $this->cspID;}
	public function getCustomStylePresetName() {return $this->cspName;}
	public function getCustomStylePresetRuleID() {return $this->csrID;}
	public function getCustomStylePresetRuleObject() {return CustomStyleRule::getByID($this->csrID);}

	public static function getByID($cspID) {
		$csp = new CustomStylePreset();
		$csp->load($cspID);
		if (is_object($csp) && $csp->getCustomStylePresetID() == $cspID) {
			return $csp;
		}
	}
	
	public function load($cspID) {
		$db = Loader::db();
		$r = $db->GetRow('select cspID, cspName, csrID from CustomStylePresets where cspID  = ?', array($cspID));
		if (is_array($r) && $r['cspID'] > 0) {
			$this->setPropertiesFromArray($r);
		}
	}
	
	/** 
	 * Removes a preset. Does NOT remove the associated rule
	 */
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CustomStylePresets where cspID = ?', array($this->cspID));
	}
	
	public function add($cspName, $csr) {
		$db = Loader::db();
		$db->Execute('insert into CustomStylePresets (cspName, csrID) values (?, ?)', array(
			$cspName,
			$csr->getCustomStyleRuleID()
		));
	
	}
	
}