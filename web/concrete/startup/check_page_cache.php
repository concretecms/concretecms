<?
defined('C5_EXECUTE') or die("Access Denied.");
$request = Request::getInstance();
$library = PageCache::getLibrary();
if ($library->shouldCheckCache($request)) {
    $record = $library->getRecord($request);
    if ($record instanceof PageCacheRecord) {
    	if ($record->validate()) {
	    	$library->deliver($record);
			require(DIR_BASE_CORE . '/startup/shutdown.php');
			exit;
	    }
    }
}