<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardSitemapAccessController extends Controller {

	var $helpers = array('form', 'validation/token');
	
	public function on_start() {
		$this->set('h', Loader::helper('concrete/dashboard/task_permissions'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dashboard.permissions.js'));
	}

	public function save_permissions() {
		$vt = Loader::helper('validation/token');
		
		if (!$vt->validate("sitemap_permissions")) {
			$this->set('error', array($vt->getErrorMessage()));
			return;
		}	
		
		$post = $this->post();
		
		$h = Loader::helper('concrete/dashboard/task_permissions');
		$h->save($post);
		$this->redirect('/dashboard/sitemap/access', 'permissions_saved');
	}
	
	public function permissions_saved() {
		$this->set('message', t('Sitemap Permissions saved.'));
	}
}