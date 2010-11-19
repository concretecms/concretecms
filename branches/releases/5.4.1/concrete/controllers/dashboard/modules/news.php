<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class NewsDashboardModuleController extends Controller {

	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/news/feed';
	const FEED_READ_MORE = "http://www.concrete5.org/community/";
	
	public function __construct() {
		Loader::model('system_notification');
		$snl = new SystemNotificationList();
		$snl->setItemsPerPage(4);
		$notifications = $snl->getPage();
		$this->set('notifications', $notifications);
		$this->set('total', $snl->getTotal());
	}
}