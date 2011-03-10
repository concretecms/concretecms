<?

if (!isset($disableTrackingCode) || $disableTrackingCode == false) {
	echo Config::get('SITE_TRACKING_CODE');
}

// not working yet
// print $this->outputFooterItems();