<?php 

class ActivityDashboardModuleController extends Controller {

	public function __construct() {
		Loader::model('user_statistics');
		Loader::model('page_statistics');
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$us = new UserStatistics($ui);
		$this->set('uLastActivity', $us->getPreviousSessionPageViews());
		$this->set('uLastLogin', date('F d, Y', $ui->getLastLogin()) . ' at ' . date('g:i A', $ui->getLastLogin()));
		$this->set('uName', $ui->getUserName());
		$this->set('totalViews', PageStatistics::getTotalPageViewsForOthers($u));
		$this->set('totalVersions', PageStatistics::getTotalPageVersions());
		
		$sle = strtotime(PageStatistics::getSiteLastEdit());
		$this->set('lastEditSite', date('F d, Y', $sle) . ' at ' . date('g:i A', $sle));
		
		$llu = UserStatistics::getLastLoggedInUser();
		if ($llu->getUserID() == $u->getUserID()) {
			$this->set('lastLoginSite', 'Your login is the most recent.');		
		} else { 
			$this->set('lastLoginSite', date('F d, Y', $llu->getLastLogin()) . ' at ' . date('g:i A', $llu->getLastLogin()));
		}

		$this->set('totalEditMode', PageStatistics::getTotalPagesCheckedOut());

		Loader::block('form');
		
		$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
		$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		
	}
	
}

?>