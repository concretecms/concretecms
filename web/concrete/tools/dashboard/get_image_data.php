<?
defined('C5_EXECUTE') or die("Access Denied.");
session_write_close();

// first, we check to see if the dashboard image data has been set in the cache
if ($_REQUEST['image'] && preg_match('/([0-9]+)\.jpg/i', $_REQUEST['image'])) { 
	$imageData = Cache::get('dashboard_image_data', $_REQUEST['image']);
	if (!$imageData) {
		// call out to the server to grab the data
		$imageData = Loader::helper('file')->getContents(DASHBOARD_BACKGROUND_INFO . '?image=' . $_REQUEST['image']);
		Cache::set('dashboard_image_data', $_REQUEST['image'], $imageData);
	}
}

print $imageData;