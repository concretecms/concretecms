<?php
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardWorkflowMeController extends DashboardBaseController {

	protected $wpCategoryHandleActive = 'page';
	
	public function on_start() {
		parent::on_start();
		$this->categories = WorkflowProgressCategory::getList();
		foreach($this->categories as $cat) {
			$this->categoryHandles[] = $cat->getWorkflowProgressCategoryHandle();
		}
	}
	
	public function on_before_render() {
		$tabs = array();
		foreach($this->categories as $cat) { 
			$active = ($cat->getWorkflowProgressCategoryHandle() == $this->wpCategoryHandleActive);
			if ($active) { 
				$this->set('category', $cat);
			}
			$tabs[] = array(View::url('/dashboard/workflow/me/', 'view', $cat->getWorkflowProgressCategoryHandle()), t('%ss', Loader::helper('text')->unhandle($cat->getWorkflowProgressCategoryHandle())), $active);
		}
		$this->set('tabs', $tabs);
	}
	
	public function view($wpCategoryHandle = false) {
		if (in_array($wpCategoryHandle, $this->categoryHandles)) {
			$this->wpCategoryHandleActive = $wpCategoryHandle;		
		}
	}
	
	public function workflow_action($category = false, $wpID = false, $task = false, $token = false) {
		if (Loader::helper('validation/token')->validate(false, $token)) { 
			$class = Loader::helper('text')->camelcase($category) . 'WorkflowProgress';
			$wp = call_user_func_array(array($class, 'getByID'), array($wpID));
			if (is_object($wp) && $task) {
				$r = $wp->runTask($task);
				$this->redirect('/dashboard/workflow/me');			
			}		
		}
	}
	
	
}