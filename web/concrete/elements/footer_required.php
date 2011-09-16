<?

if (!isset($disableTrackingCode) || $disableTrackingCode == false) {
	echo Config::get('SITE_TRACKING_CODE');
}

print $this->controller->outputFooterItems();

?>