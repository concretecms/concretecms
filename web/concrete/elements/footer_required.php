<?

$c = Page::getCurrentPage();
$cp = new Permissions($c);

Loader::element('page_controls_footer', array('cp' => $cp, 'c' => $c));

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('SITE_TRACKING_CODE');
}

$v = View::getInstance();
print $v->markFooterAssetPosition();

// user profile menu
Loader::element('account/menu');
?>
