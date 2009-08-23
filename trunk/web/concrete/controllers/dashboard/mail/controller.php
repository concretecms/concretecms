<?
defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardMailController extends Controller {


	public function __construct() { 
		$this->redirect('/dashboard/mail/importers');	
	}
	
}

?>