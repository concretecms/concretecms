<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Blocks_Stacks extends DashboardBaseController {

	
	public function on_start() {
		parent::on_start();
		Loader::model('stack/list');
		$stm = new StackList();
		$stm->filterByGlobalAreas();
		$this->set('globalareas', $stm->get());

		$stm = new StackList();
		$stm->filterByUserAdded();
		$this->set('useradded', $stm->get());

	}		
	
	public function add_stack() {
		if (Loader::helper('validation/token')->validate('add_stack')) {
			if (Loader::helper('validation/strings')->notempty($this->post('stackName')))  {
				$stack = Stack::addStack($this->post('stackName'));
				$this->redirect('/dashboard/blocks/stacks', 'stack_added');
			} else {
				$this->error->add(t("You must give your stack a name."));
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
	public function stack_added() {
		$this->set('message', t('Stack added successfully'));
	}

	public function stack_deleted() {
		$this->set('message', t('Stack deleted successfully'));
	}
	
	public function delete($cID = false, $token = false) {
		if (Loader::helper('validation/token')->validate('delete', $token)) {
			$s = Stack::getByID($cID);
			if (is_object($s)) {
				$sps = new Permissions($s);
				if ($sps->canDeletePage()) {
					$u = new User();
					$pkr = new DeletePagePageWorkflowRequest();
					$pkr->setRequestedPage($s);
					$pkr->setRequesterUserID($u->getUserID());
					$response = $pkr->trigger();
					if ($response instanceof WorkflowProgressResponse) {
						// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
						$this->redirect('/dashboard/blocks/stacks', 'stack_deleted');
					} else {
						$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID, 'delete_saved');
					}
				} else {
					$this->error->add(t('You do not have access to delete this stack.'));
				}
			} else {
				$this->error->add(t('Invalid stack'));
			}
		} else {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
	}
	
	public function view_details($cID, $msg = false) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$blocks = $s->getBlocks('Main');
			$view = View::getInstance();
			foreach($blocks as $b1) {
				$btc = $b1->getInstance();
				// now we inject any custom template CSS and JavaScript into the header
				if('Controller' != get_class($btc)){
					$btc->outputAutoHeaderItems();
				}
				$btc->runTask('on_page_view', array($view));
			}
			$this->addHeaderItem('<style type="text/css">' . $s->outputCustomStyleHeaderItems(true) . '</style>');

			$this->set('stack', $s);
			$this->set('blocks', $blocks);
			switch($msg) {
				case 'delete_saved':
					$this->set('message', t('Delete request saved. You must complete the delete workflow before this stack can be deleted.'));
					break;
			
			}
		} else {
			throw new Exception(t('Invalid stack'));
		}
	}
	
}