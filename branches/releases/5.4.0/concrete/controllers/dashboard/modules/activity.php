<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
class ActivityDashboardModuleController extends Controller {

	public function __construct() {
		Loader::model('user_statistics');
		Loader::model('page_statistics');
		
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$us = new UserStatistics($ui);
		$this->set('uLastActivity', $us->getPreviousSessionPageViews());
		$this->set('uLastLogin', strftime('%x ' . t('at') . ' %l:%M %p', $ui->getLastLogin('user')));
		$this->set('uName', $ui->getUserName());
		$this->set('totalViews', PageStatistics::getTotalPageViewsForOthers($u));
		$this->set('totalVersions', PageStatistics::getTotalPageVersions());
		
		$this->set('lastEditSite', strftime('%x ' . t('at') . ' %l:%M %p', strtotime(PageStatistics::getSiteLastEdit('user'))));
		$llu = UserStatistics::getLastLoggedInUser();
		if ($llu->getUserID() == $u->getUserID()) {
			$this->set('lastLoginSite', t('Your login is the most recent.'));		
		} else { 
			$this->set('lastLoginSite', strftime('%x ' . t('at') . ' %l:%M %p', $llu->getLastLogin()));
		}

		$this->set('totalEditMode', PageStatistics::getTotalPagesCheckedOut());

		Loader::block('form');
		
		$this->set('totalFormSubmissions', FormBlockStatistics::getTotalSubmissions());
		$this->set('totalFormSubmissionsToday', FormBlockStatistics::getTotalSubmissions(date('Y-m-d')));
		
	}
	
}

?>