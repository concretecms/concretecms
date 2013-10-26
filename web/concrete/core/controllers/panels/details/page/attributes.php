<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Attributes extends PanelController {

	protected $viewPath = '/system/panels/details/page/attributes';

	protected function canViewPanel() {
		return $this->permissions->canEditPageProperties();
	}

	public function __construct() {
		parent::__construct();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($c);
		$this->assignment = $pk->getMyAssignment();
	}

	public function view() {
		$this->requireAsset('javascript', 'underscore');
		$this->set('assignment', $this->assignment);
		$this->set('dt', Loader::helper('form/date_time'));
		$this->set('uh', Loader::helper('form/user_selector'));
	}

	public function submit() {
		if ($this->validateSubmitPanel()) {

		}
	}

	/** 
	 * Retrieve attribute HTML to inject into the other view.
	 */
	public function add_attribute() {
		$allowed = $this->assignment->getAttributesAllowedArray();
		$ak = CollectionAttributeKey::getByID($this->request->request->get('akID'));
		ob_start();
		if (is_object($ak) && in_array($ak->getAttributeKeyID(), $allowed)) {
			$av = new AttributeTypeView($ak);
			$html = $av->render('form');
		}
		$html = ob_get_contents();
		ob_end_clean();
		$obj = new stdClass;
		$obj->akID = $ak->getAttributeKeyID();
		$obj->label = $ak->getAttributeKeyName();
		$obj->content = $html;
		$obj->pending = true;
		Loader::helper('ajax')->sendResult($obj);
	}



}