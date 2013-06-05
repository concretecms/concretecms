<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System_Basics_SiteName extends DashboardBaseController {

	public function view() {
		$this->set('site', SITE);
	}

	public function sitename_saved() {
		$this->set('message', t("Your site's name has been saved."));	
		$this->view();
	}
	
	public function update_sitename() {
		if ($this->token->validate("update_sitename")) {
			if ($this->isPost()) {
				Config::save('SITE', $this->post('SITE'));
				$this->redirect('/dashboard/system/basics/site_name','sitename_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}


}