<? 

/**
 * @package Users
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 

class Concrete5_Model_LayoutPreset extends Object{
	
	protected $lpID=0;
	protected $lpName='';
	protected $layoutID=0;
	 
	public function getLayoutPresetID(){ return $this->lpID; }
	public function getLayoutPresetName(){ return $this->lpName; }
	public function getLayoutID(){ return $this->layoutID; }
	public function getLayoutObject() { return Layout::getById($this->layoutID); }	 
 	
	static public function getList() {
		$db = Loader::db();
		$r = $db->Execute('select lp.* FROM LayoutPresets AS lp, Layouts AS l WHERE lp.layoutID=l.layoutID order by lpName asc');
		$presets = array();
		while ($row = $r->FetchRow()) {
			$layoutPreset = new LayoutPreset();
			$layoutPreset->setPropertiesFromArray($row);
			$presets[] = $layoutPreset;
		}
		return $presets;
	}
 	
	public static function getByID($lpID) { 
		$db = Loader::db();
		$r = $db->GetRow('SELECT lp.* FROM LayoutPresets AS lp, Layouts AS l WHERE lp.layoutID=l.layoutID AND lp.lpID  = '.intval($lpID));
		if(is_array($r) && intval($r['lpID']) ) {
			$layoutPreset = new LayoutPreset(); 
			$layoutPreset->setPropertiesFromArray($r);
			return $layoutPreset;
		} 
		return false; 
	}
	
	//Removes a preset. Does NOT remove the associated rule
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from LayoutPresets where lpID = '.intval($this->lpID) );
	}	 
	
	public function add($lpName, $layout) {
		$db = Loader::db();
		$db->Execute('insert into LayoutPresets (lpName, layoutID) values (?, ?)', array( $lpName, $layout->getLayoutID() )  ); 
	} 
}