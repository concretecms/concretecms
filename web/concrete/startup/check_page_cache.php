<?
defined('C5_EXECUTE') or die("Access Denied.");
$request = Request::get();
$library = PageCache::getLibrary();
if ($library->shouldCheckCache($request)) {
    $record = $library->getRecord($request);
    if ($record instanceof PageCacheRecord) {
    	if ($record->validate()) {
	    	$library->deliver($record);
			if (ob_get_level() == OB_INITIAL_LEVEL) {
				require(DIR_BASE_CORE . '/startup/shutdown.php');
				exit;
			}
	    	exit;
	    }
    }
}