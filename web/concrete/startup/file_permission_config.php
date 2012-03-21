<?php defined('C5_EXECUTE') or die("Access Denied.");

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
