<?php
defined('C5_EXECUTE') or die("Access Denied.");
session_write_close();

// first, we check to see if the dashboard image data has been set in the cache
if (DASHBOARD_BACKGROUND_INFO != false) {
	if ($_REQUEST['image'] && preg_match('/([0-9]+)\.jpg/i', $_REQUEST['image'])) {
        /** @var \Stash\Interfaces\ItemInterface $imageDataCache */
        $imageDataCache = Core::make('cache')->getItem('dashboard_image_data/' . $_REQUEST['image']);
		if ($imageDataCache->isMiss()) {
			// call out to the server to grab the data
            $imageDataCache->lock();
			$cfToken = Marketplace::getSiteToken();
            $imageData = Loader::helper('file')->getContents(Config::get('concrete.urls.background_info') . '?image=' . $_REQUEST['image'] . '&cfToken=' . $cfToken);
            $imageDataCache->set($imageData);
		} else {
            $imageData = $imageDataCache->get();
        }
	}

	print $imageData;

}
