<?php  defined('C5_EXECUTE') or die("Access Denied.");

if (!defined('FILE_PERMISSIONS_MODE')) {
	$perm = Loader::helper('file')->getCreateFilePermissions()->file;
	if($perm) {
		define('FILE_PERMISSIONS_MODE', $perm);
	} else {
		define('FILE_PERMISSIONS_MODE', 0664);
	}
}

if (!defined('DIRECTORY_PERMISSIONS_MODE')) {
	$perm = Loader::helper('file')->getCreateFilePermissions()->dir;
	if($perm) {
		define('DIRECTORY_PERMISSIONS_MODE', $perm);
	} else {
		define('DIRECTORY_PERMISSIONS_MODE', 0775);
	}
}

if (defined('DIR_FILES_CACHE') && !is_dir(DIR_FILES_CACHE)) {
	@mkdir(DIR_FILES_CACHE);
	@chmod(DIR_FILES_CACHE, DIRECTORY_PERMISSIONS_MODE);
	@touch(DIR_FILES_CACHE . '/index.html');
	@chmod(DIR_FILES_CACHE . '/index.html', FILE_PERMISSIONS_MODE);
}

# Sessions/TMP directories
if (!defined('DIR_SESSIONS')) {
	define('DIR_SESSIONS', Loader::helper('file')->getTemporaryDirectory());
}