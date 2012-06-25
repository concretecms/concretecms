<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users extends Controller {


	public function __construct() { 
		$this->redirect('/dashboard/users/search');	
	}
	
}