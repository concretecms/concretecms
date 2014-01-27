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
				if (Loader::helper('validation/strings')->alphanum($this->post('SITE'))) {
					Config::save('SITE', $this->post('SITE'));
					$this->redirect('/dashboard/system/basics/site_name','sitename_saved');
				} else {
					$this->error->add(t('Your site name may only contain letters and numbers.'));
				}
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}


}