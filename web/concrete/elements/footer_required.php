<?php
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($c)) {
    $cp = new Permissions($c);
    View::element('page_controls_footer', array('cp' => $cp, 'c' => $c));
}

$_trackingCodePosition = Config::get('concrete.seo.tracking.code_position');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('concrete.seo.tracking.code');
}

View::getInstance()->markFooterAssetPosition();

// user profile menu
View::element('account/menu');
