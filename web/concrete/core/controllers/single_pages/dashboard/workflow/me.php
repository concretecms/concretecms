<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Workflow_Me extends DashboardBaseController {

	protected $wpCategoryHandleActive = 'page';
	
	public function on_start() {
		parent::on_start();
		$this->addHeaderItem("<script type=\"text/javascript\">$(function() { $('.dialog-launch').dialog();});</script>");
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
		
	
}