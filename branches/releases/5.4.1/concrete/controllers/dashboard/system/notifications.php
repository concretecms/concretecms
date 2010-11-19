<?php 

defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('system_notification');
Loader::controller("/dashboard/page");

class DashboardSystemNotificationsController extends DashboardPageController { 	 
	
	function view() {  
		$snl = new SystemNotificationList();
		$this->set('notifications', $snl->get());

	}
	
}