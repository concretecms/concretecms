<?

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('SITE_TRACKING_CODE');
}

print $this->controller->outputFooterItems();

?>