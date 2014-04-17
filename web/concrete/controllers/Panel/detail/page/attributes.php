<?
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use PageEditResponse;
use PermissionKey;
use stdClass;
use Loader;
use CollectionAttributeKey;
use \Concrete\Core\Attribute\View as AttributeTypeView;

class Attributes extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/attributes';

	protected function canAccess() {
		return $this->permissions->canEditPageProperties();
	}

	public function __construct() {
		parent::__construct();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($this->page);
		$this->assignment = $pk->getMyAssignment();
	}

	protected function getAttributeJSONRepresentation(CollectionAttributeKey $ak, $mode = 'edit') {
		ob_start();
		$av = new AttributeTypeView($ak);
		if ($mode == 'edit') {
			$caValue = $this->page->getAttributeValueObject($ak);
			$ak->render('form', $caValue);
		} else {
			print $av->render('form');
		}
		$html = ob_get_contents();
		ob_end_clean();
		$obj = new stdClass;
		$obj->akID = $ak->getAttributeKeyID();
		$obj->label = $ak->getAttributeKeyName();
		$obj->content = $html;
		$obj->pending = ($mode == 'add') ? true : false;
		return $obj;		
	}

	public function view() {
		$this->set('assignment', $this->assignment);
		$this->set('dt', Loader::helper('form/date_time'));
		$this->set('uh', Loader::helper('form/user_selector'));
		$selectedAttributes = array();
		$allowed = $this->assignment->getAttributesAllowedArray();
		foreach($this->page->getSetCollectionAttributes() as $ak) {
			if (is_object($ak) && in_array($ak->getAttributeKeyID(), $allowed)) {
				$obj = $this->getAttributeJSONRepresentation($ak);
				$selectedAttributes[] = $obj;
			}
		}
		$this->set('selectedAttributes', Loader::helper('json')->encode($selectedAttributes));
	}

	public function submit() {
		if ($this->validateAction()) {
			$c = $this->page;
			$cp = $this->permissions;
			$asl = $this->assignment;

			$nvc = $c->getVersionToModify();
			$data = array();
			if ($asl->allowEditName()) { 
				$data['cName'] = $_POST['cName'];
			}
			if ($asl->allowEditDescription()) { 
				$data['cDescription'] = $_POST['cDescription'];
			}
			if ($asl->allowEditDateTime()) { 
				$dt = Loader::helper('form/date_time');
				$dh = Loader::helper('date');
				$data['cDatePublic'] = $dh->getSystemDateTime($dt->translate('cDatePublic'));
			}
			if ($asl->allowEditUserID()) { 
				$data['uID'] = $_POST['uID'];
			}
			
			$nvc->update($data);
			
			// First, we check out the attributes we need to clear.
			$setAttribs = $nvc->getSetCollectionAttributes();
			$processedAttributes = array();
			$selectedAKIDs = $_POST['selectedAKIDs'];
			if (!is_array($selectedAKIDs)) {
				$selectedAKIDs = array();					
			}
			foreach($setAttribs as $ak) {
				// do I have the ability to edit this attribute?
				if (in_array($ak->getAttributeKeyID(), $asl->getAttributesAllowedArray())) {
					// Is this item in the selectedAKIDs array? If so then it is being saved
					if (in_array($ak->getAttributeKeyID(), $_POST['selectedAKIDs'])) {
						$ak->saveAttributeForm($nvc);
					} else {
						// it is being removed
						$nvc->clearAttribute($ak);
					}
					$processedAttributes[] = $ak->getAttributeKeyID();
				}					
			}
			$newAttributes = array_diff($selectedAKIDs, $processedAttributes);
			foreach($newAttributes as $akID) {
				if ($akID > 0 && in_array($akID, $asl->getAttributesAllowedArray())) {
					$ak = CollectionAttributeKey::getByID($akID);
					$ak->saveAttributeForm($nvc);
				}
			}

			$r = new PageEditResponse();
			$r->setPage($c);
			$r->setMessage(t('Page attributes saved.'));
			$r->outputJSON();
		}
	}

	/** 
	 * Retrieve attribute HTML to inject into the other view.
	 */
	public function add_attribute() {
		$allowed = $this->assignment->getAttributesAllowedArray();
		$ak = CollectionAttributeKey::getByID($_REQUEST['akID']);
		if (is_object($ak) && in_array($ak->getAttributeKeyID(), $allowed)) {
			$obj = $this->getAttributeJSONRepresentation($ak, 'add');
			$obj->pending = true;
			Loader::helper('ajax')->sendResult($obj);
		}
	}



}