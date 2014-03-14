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
	
	public function view() {
		$parent = Page::getByPath(STACKS_PAGE_PATH);
		$cpc = new Permissions($parent);
		if ($cpc->canMoveOrCopyPage()) {
			$this->set('canMoveStacks', true);
			
			$sortUrl = View::url('/dashboard/blocks/stacks', 'update_order');
			$this->addFooterItem('<script type="text/javascript">
			$("div.ccm-stack-content-wrapper").sortable({
				handle: "img.ccm-group-sort",
				cursor: "move",
				axis: "y",
				opacity: 0.5,
				stop: function() {
					var pagelist = $(this).sortable("serialize");
					$.ajax({
						dataType: "json",
						type: "post",
						url: "'.$sortUrl.'",
						data: pagelist,
						success: function(r) {
							if (r.success) {
								ccmAlert.hud(r.message, 2000, "success");
							} else {
								ccmAlert.hud(r.message, 2000, "error");
							}
						}
					});
				}
			});
			</script>');
		}
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
		$this->view();
	}

	public function stack_deleted() {
		$this->set('message', t('Stack deleted successfully'));
		$this->view();
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
				case 'rename_saved':
					$this->set('message', t('Rename request saved. You must complete the approval workflow before the name of the stack will be updated.'));
					break;
			}
		} else {
			throw new Exception(t('Invalid stack'));
		}
	}
	
	public function rename($cID) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$this->set('stack', $s);
		} else {
			throw new Exception(t('Invalid stack'));
		}
		$sps = new Permissions($s);
		if (!$sps->canEditPageProperties()) {
			$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
		}
		
		if ($this->isPost()) {
			if (Loader::helper('validation/token')->validate('rename_stack')) {
				if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName')))) {
					$txt = Loader::helper('text');
					$v = $s->getVersionToModify();
					$v->update(array(
						'cName' => $stackName,
						'cHandle' => str_replace('-', PAGE_PATH_SEPARATOR, $txt->urlify($stackName))
					));
					
					$u = new User();
					$pkr = new ApproveStackPageWorkflowRequest();
					$pkr->setRequestedPage($s);
					$pkr->setRequestedVersionID($v->getVersionID());
					$pkr->setRequesterUserID($u->getUserID());
					$response = $pkr->trigger();
					if ($response instanceof WorkflowProgressResponse) {
						// we only get this response if we have skipped workflows and jumped straight in to an approve() step.
						$this->redirect('/dashboard/blocks/stacks', 'stack_renamed', $cID);
					} else {
						$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID, 'rename_saved');
					}
				} else {
					$this->error->add(t("The stack name cannot be empty."));
				}
			} else {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
		}
	}
	
	public function stack_renamed($cID) {
		$this->set('message', t('Stack renamed successfully'));
		$this->view_details($cID);
		$this->task = 'view_details';
	}
	
	public function duplicate($cID) {
		$s = Stack::getByID($cID);
		if (is_object($s)) {
			$this->set('stack', $s);
		} else {
			throw new Exception(t('Invalid stack'));
		}
		$sps = new Permissions($s);
		if (!$sps->canMoveOrCopyPage()) {
			$this->redirect('/dashboard/blocks/stacks', 'view_details', $cID);
		}
		
		if ($this->isPost()) {
			if (Loader::helper('validation/token')->validate('duplicate_stack')) {
				if (Loader::helper('validation/strings')->notempty($stackName = trim($this->post('stackName'))))  {
					$ns = $s->duplicate();
					$ns->update(array(
						'stackName' => $stackName
					));
					
					$this->redirect('/dashboard/blocks/stacks', 'stack_duplicated');
				} else {
					$this->error->add(t("You must give your stack a name."));
				}
			} else {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
			$name = trim($this->post('name'));
		}
	}
	
	public function stack_duplicated() {
		$this->set('message', t('Stack duplicated successfully'));
		$this->view();
	}
	
	public function update_order() {
		$ret = array('success' => false, 'message' => t("Error"));
		if ($this->isPost() && is_array($stIDs = $this->post('stID'))) {
			$parent = Page::getByPath(STACKS_PAGE_PATH);
			$cpc = new Permissions($parent);
			if ($cpc->canMoveOrCopyPage()) {
				foreach($stIDs as $displayOrder => $cID) { 
					$c = Page::getByID($cID);
					$c->updateDisplayOrder($displayOrder, $cID);
				}
				$ret['success'] = true;
				$ret['message'] = t("Stack order updated successfully.");
			}
		}
		echo Loader::helper('json')->encode($ret);
		exit;
	}
	
}