<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardSitemapController extends Controller {

	public function view() {
		$this->redirect('/dashboard/sitemap/full');
	}
	
}