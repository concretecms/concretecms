<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class ActivityDashboardModuleController extends Controller {

	public function __construct() {
		
		Loader::model('user_statistics');
		Loader::model('page_statistics');
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$us = new UserStatistics($ui);
		$this->set('uLastActivity', $us->getPreviousSessionPageViews());
		$timeStr = '';
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { 
			$timeStr = '%x ' . t('at') . ' %I:%M %p';
		} else {
			$timeStr = '%x ' . t('at') . ' %l:%M %p';
		}		
		$this->set('uLastLogin', strftime($timeStr, $ui->getLastLogin('user')));
		$this->set('uName', $ui->getUserName());
		$this->set('totalViews', PageStatistics::getTotalPageViewsForOthers($u));
		$this->set('totalVersions', PageStatistics::getTotalPageVersions());
		
		$this->set('lastEditSite', strftime($timeStr, strtotime(PageStatistics::getSiteLastEdit('user'))));
		$llu = UserStatistics::getLastLoggedInUser();
		if ($llu->getUserID() == $u->getUserID()) {
			$this->set('lastLoginSite', t('Your login is the most recent.'));		
		} else { 
			$this->set('lastLoginSite', strftime($timeStr, $llu->getLastLogin()));
		}

		$this->set('totalEditMode', PageStatistics::getTotalPagesCheckedOut());

		Loader::block('form');
		
		$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
		$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		
	}
	
}

?>