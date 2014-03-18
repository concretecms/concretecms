<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Page_AddBlock extends BackendInterfacePageController {

	protected $viewPath = '/system/dialogs/page/add_block';

	protected function canAccess() {
		return $this->areaPermissions->canAddBlock($this->blockType);
	}

	public function __construct() {
		parent::__construct();
		$request = $this->request;
		$cID = $request->query->get('cID');

		if (!Loader::helper('validation/numbers')->integer($request->query->get('btID'))) {
			throw new Exception(t('Access Denied'));
		}

		$this->blockType = BlockType::getByID($request->query->get('btID'));
		$this->area = Area::get($this->page, $request->query->get('arHandle'));
		$this->areaPermissions = new Permissions($this->area);
		$cnt = $this->blockType->getController();
		if (!is_a($cnt, 'BlockController')) {
			throw new Exception(t('Unable to load the controller for this block type. Perhaps it has been moved or removed.'));
		}
		$this->blockTypeController = $cnt;
	}

	public function view() {
		$bv = new BlockView($this->blockType);
		$bv->setAreaObject($this->area);
		// Handle special posted area parameters here
		if (isset($_REQUEST['arGridColumnSpan'])) {
			$this->area->setAreaGridColumnSpan(intval($_REQUEST['arGridColumnSpan']));
		}
		$bv->addScopeItems(array('a' => $this->a, 'cp' => $this->permissions, 'ap' => $this->areaPermissions));
		$this->set('blockView', $bv);
		$this->set('blockType', $this->blockType);
		$this->set('btHandle', $this->blockType->getBlockTypeHandle());
		$this->set("blockTypeController", $this->blockTypeController);
		$this->set('area', $this->area);
	}

	public function submit() {
		if ($this->validateAction()) {
			$c = $this->page;
			$cp = $this->permissions;
			$u = new User();
			if ($cp->canDeletePage() && $c->getCollectionID() != HOME_CID && (!$c->isMasterCollection())) {
				$children = $c->getNumChildren();
				if ($children == 0 || $u->isSuperUser()) {
					if ($c->isExternalLink()) {
						$c->delete();
					} else { 
						$pkr = new DeletePagePageWorkflowRequest();
						$pkr->setRequestedPage($c);
						$pkr->setRequesterUserID($u->getUserID());
						$u->unloadCollectionEdit($c);
						$response = $pkr->trigger();
						$pr = new PageEditResponse();
						$pr->setPage($c);
						$parent = Page::getByID($c->getCollectionParentID(), 'ACTIVE');
						if ($response instanceof WorkflowProgressResponse) {
							// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
							$pr->setMessage(t('Page deleted successfully.'));
							$pr->setRedirectURL(BASE_URL . '/' . DISPATCHER_FILENAME . '?cID=' . $parent->getCollectionID());
						} else {
							$pr->setMessage(t('Page request saved. This action will have to be approved before the page is deleted.'));
						}
						$pr->outputJSON();
					}
				}
			}
		}
	}

}

