<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($c)) {
    $cp = new Permissions($c);
    View::element('page_controls_footer', array('cp' => $cp, 'c' => $c));
}

if (empty($disableTrackingCode)) {
    echo Config::get('concrete.seo.tracking.code.footer');
}

$dh = Core::make('helper/concrete/dashboard');

View::getInstance()->markFooterAssetPosition();

// user profile menu
if (!$dh->inDashboard($c)) {
    View::element('account/menu');
}
