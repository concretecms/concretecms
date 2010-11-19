<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class HelpDashboardModuleController extends Controller {
	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/help/feed';
	
	public function __construct() {
		/*
		Loader::model('system_notification');
		$snl = new SystemNotificationList();
		$snl->filterByType(SystemNotification::SN_TYPE_CORE_MESSAGE_HELP);
		$snl->setItemsPerPage(2);
		$posts = $snl->getPage();
		$this->set('posts', $posts);
		*/
	}
}