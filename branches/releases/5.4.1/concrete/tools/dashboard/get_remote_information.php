<?php 
defined('C5_EXECUTE') or die("Access Denied.");

Loader::dashboardModuleController('help');
Loader::dashboardModuleController('news');
Loader::model('system_notification');

// latest version, including addon updates
Loader::library('update');
$lv = Update::getLatestAvailableVersionNumber();

$fp = Loader::helper("feed");
$feed = $fp->load(HelpDashboardModuleController::FEED);
$feed->set_timeout(3);
$posts = $feed->get_items(0, 5);
foreach($posts as $p) {
	SystemNotification::addFromFeed($p, SystemNotification::SN_TYPE_CORE_MESSAGE_HELP);
}

$feed = $fp->load(NewsDashboardModuleController::FEED);
$feed->set_timeout(3);
$posts = $feed->get_items(0, 5);

foreach($posts as $p) {
	SystemNotification::addFromFeed($p, SystemNotification::SN_TYPE_CORE_MESSAGE_NEWS);
}