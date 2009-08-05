<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DateAttributeTypeController extends AttributeTypeController  {

	public $helpers = array('form');
	
	public function saveKey() {
		$ak = $this->getAttributeKey();
		
		$db = Loader::db();

		$akDateDisplayMode = $this->post('akDateDisplayMode');
				
		// now we have a collection attribute key object above.
		$db->Replace('atDateSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akDateDisplayMode' => $akDateDisplayMode
		), array('akID'), true);
	}
	
	public function type_form() {
		$this->load();
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('select akDateDisplayMode from atDateSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akDateDisplayMode = $row['akDateDisplayMode'];

		$this->set('akDateDisplayMode', $this->akDateDisplayMode);
	}
	

}