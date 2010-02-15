<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class NewsDashboardModuleController extends Controller {

	// simple pie is awesome and parses the HTML!
	const FEED = 'http://www.concrete5.org/community/updates/news/feed';
	const FEED_READ_MORE = "http://www.concrete5.org/community/";
	
	public function __construct() {
		Loader::model('system_notification');
		$snl = new SystemNotificationList();
		$snl->filterByType(SystemNotification::SN_TYPE_CORE_MESSAGE_NEWS);
		$snl->setItemsPerPage(2);
		$posts = $snl->getPage();
		$this->set('posts', $posts);
		$this->set('feed_read_more', NewsDashboardModuleController::FEED_READ_MORE);
	}
}