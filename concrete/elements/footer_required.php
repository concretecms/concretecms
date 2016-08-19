<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
$site = Core::make('site')->getSite();

if (is_object($c)) {
    $cp = new Permissions($c);
    View::element('page_controls_footer', array('cp' => $cp, 'c' => $c));
}

if (empty($disableTrackingCode)) {
    print $site->getConfigRepository()->get('seo.tracking.code.footer');
}

$dh = Core::make('helper/concrete/dashboard');

View::getInstance()->markFooterAssetPosition();

// user profile menu
if (!$dh->inDashboard($c)) {
    View::element('account/menu');
}
