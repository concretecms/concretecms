<?php

$c = Page::getCurrentPage();
if (is_object($c)) {
    $cp = new Permissions($c);
    Loader::element('page_controls_footer', array('cp' => $cp, 'c' => $c));
}

$_trackingCodePosition = Config::get('concrete.seo.tracking.code_position');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('concrete.seo.tracking.code');
}

$v = View::getInstance();
print $v->markFooterAssetPosition();

// user profile menu
Loader::element('account/menu');
