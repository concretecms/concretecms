<?php 
/**
 *
 * When this file is run it basically queries the database for site config items and sets those up, possibly overriding items in the base.php.
 * The hierarchy basically goes like this:
 * 1. Item defined in config/site.php? Then it will be used.
 * 2. Item saved in database? Then it will be used.
 * 3. Otherwise, we setup the defaults below.
 **/
defined('C5_EXECUTE') or die(_("Access Denied.")); 

// We define these here because we want to be able to turn them off during installation
if (!defined('ENABLE_CACHE')) {
	define('ENABLE_CACHE', true);
}

# permissions model - valid options are 'advanced' or 'simple'
if (!defined('PERMISSIONS_MODEL')) {
	Config::getOrDefine('PERMISSIONS_MODEL', 'simple');
}

if (!defined('SITE')) {
	Config::getOrDefine('SITE', 'Concrete5');
}

if (!defined('ENABLE_LOG_EMAILS')) {
	Config::getOrDefine('ENABLE_LOG_EMAILS', true);
}

if (!defined('ENABLE_LOG_ERRORS')) {
	Config::getOrDefine('ENABLE_LOG_ERRORS', true);
}

if (!defined('ENABLE_LOG_DATABASE_QUERIES')) {
	Config::getOrDefine('ENABLE_LOG_DATABASE_QUERIES', false);
}

# Default URL rewriting setting
if (!defined('URL_REWRITING')) {
	Config::getOrDefine('URL_REWRITING', false);
}

# Default marketplace support
if (!defined('ENABLE_MARKETPLACE_SUPPORT')){  
	$marketplace_enabled=Config::get('ENABLE_MARKETPLACE_SUPPORT');
	if( $marketplace_enabled==NULL ){ 
		Config::save('ENABLE_MARKETPLACE_SUPPORT', 1 );
		$marketplace_enabled==true;
	} 
	define('MARKETPLACE_CONFIG_OVERRIDE',false);
	define('ENABLE_MARKETPLACE_SUPPORT',$marketplace_enabled); 
	//Config::getOrDefine('MARKETPLACE_ENABLED', true);	
}else{
	define('MARKETPLACE_CONFIG_OVERRIDE',true);
}

if (!defined('URL_REWRITING_ALL')) { 
	define("URL_REWRITING_ALL", false);
}

if (URL_REWRITING_ALL == true) {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/tools/required'); // front-end
} else {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/index.php/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/index.php/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/index.php/tools/required'); // front-end
}

define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
define('REL_DIR_FILES_TOOLS_PACKAGES', REL_DIR_FILES_TOOLS . '/packages'); 
