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

if (!defined('ENABLE_CACHE')) {
	Config::getOrDefine('ENABLE_CACHE', true); 
}
if (!ENABLE_CACHE) {
	Cache::disableCache();
}
# permissions model - valid options are 'advanced' or 'simple'
if (!defined('PERMISSIONS_MODEL')) {
	Config::getOrDefine('PERMISSIONS_MODEL', 'simple');
}

if (!defined('SITE')) {
	Config::getOrDefine('SITE', 'concrete5');
}

if (!defined('ENABLE_LOG_EMAILS')) {
	Config::getOrDefine('ENABLE_LOG_EMAILS', true);
}

if (!defined('ENABLE_LOG_ERRORS')) {
	Config::getOrDefine('ENABLE_LOG_ERRORS', true);
}

# Default URL rewriting setting
if (!defined('URL_REWRITING')) {
	Config::getOrDefine('URL_REWRITING', false);
}

# New date constants
if (!defined('DATE_APP_GENERIC_MDYT_FULL')) {
	define('DATE_APP_GENERIC_MDYT_FULL', t('F d, Y \a\t g:i A'));
}

if (!defined('DATE_APP_GENERIC_MDYT')) {
	define('DATE_APP_GENERIC_MDYT', t('n/j/Y \a\t g:i A'));
}

if (!defined('DATE_APP_GENERIC_MDY')) {
	define('DATE_APP_GENERIC_MDY', 'n/j/Y');
}

if (!defined('DATE_APP_GENERIC_MDY_FULL')) {
	define('DATE_APP_GENERIC_MDY_FULL', t('F d, Y'));
}

if (!defined('DATE_APP_GENERIC_T')) {
	define('DATE_APP_GENERIC_T', 'g:i A');
}

if (!defined('DATE_APP_GENERIC_TS')) {
	define('DATE_APP_GENERIC_TS', 'g:i:s A');
}

if (!defined('DATE_APP_FILENAME')) {
	define('DATE_APP_FILENAME', 'd-m-Y_H:i_'); // used when dates are used to start filenames
}

if (!defined('DATE_APP_FILE_PROPERTIES')) {
	define('DATE_APP_FILE_PROPERTIES', DATE_APP_GENERIC_MDYT_FULL);
}
if (!defined('DATE_APP_FILE_VERSIONS')) {
	define('DATE_APP_FILE_VERSIONS', DATE_APP_GENERIC_MDYT_FULL);
}
if (!defined('DATE_APP_FILE_DOWNLOAD')) {
	define('DATE_APP_FILE_DOWNLOAD', DATE_APP_GENERIC_MDYT_FULL);
}

if (!defined('DATE_APP_PAGE_VERSIONS')) {
	define('DATE_APP_PAGE_VERSIONS', DATE_APP_GENERIC_MDYT);
}
if (!defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS')) {
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS', DATE_APP_GENERIC_MDYT);
}

if (!defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES')) {
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES', DATE_APP_GENERIC_MDYT);
}

if (!defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES')) {
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES', DATE_APP_GENERIC_MDYT);
}

if (!defined('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY')) {
	define('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY', DATE_APP_GENERIC_MDY);
}
if (!defined('DATE_APP_DATE_ATTRIBUTE_TYPE_T')) {
	define('DATE_APP_DATE_ATTRIBUTE_TYPE_T', DATE_APP_GENERIC_TS);
}


if (!defined('DATE_APP_SURVEY_RESULTS')) {
	// NO DEFINE HERE, JUST PLACING HERE TO MAKE A NOTE OF IT
}

if (!defined('DATE_FORM_HELPER_FORMAT_HOUR')) {
	define('DATE_FORM_HELPER_FORMAT_HOUR', '12'); // can be 12 or 24
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

if (!defined('ENABLE_LEGACY_CONTROLLER_URLS')) {
	define('ENABLE_LEGACY_CONTROLLER_URLS', false);
}


if (URL_REWRITING_ALL == true) {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/tools/required'); // front-end
} else {
	define('URL_SITEMAP', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/sitemap');
	define('REL_DIR_FILES_TOOLS', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools');
	define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools/required'); // front-end
}

define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
define('REL_DIR_FILES_TOOLS_PACKAGES', REL_DIR_FILES_TOOLS . '/packages'); 

# File settings
if (!defined('DIR_FILES_UPLOADED')) {
	Config::getOrDefine('DIR_FILES_UPLOADED', DIR_BASE . '/files');
}

define('DIR_FILES_UPLOADED_THUMBNAILS', DIR_FILES_UPLOADED . '/thumbnails');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_FILES_UPLOADED . '/thumbnails/level2');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_FILES_UPLOADED . '/thumbnails/level3');
define('DIR_FILES_AVATARS', DIR_FILES_UPLOADED . '/avatars');

if (!defined('DIR_FILES_TRASH')) {
	define('DIR_FILES_TRASH', DIR_FILES_UPLOADED . '/trash');
}
define('DIR_FILES_INCOMING', DIR_FILES_UPLOADED . '/incoming');
define('DIR_FILES_AVATARS_STOCK', DIR_FILES_UPLOADED . '/stock_avatars');


if (DIR_FILES_UPLOADED != DIR_BASE . '/files') {
	define('ENABLE_ALTERNATE_DEFAULT_STORAGE', true);
} else {
	define('ENABLE_ALTERNATE_DEFAULT_STORAGE', false);
}

# User & Registration Settings

if (!defined('ENABLE_OPENID_AUTHENTICATION')) { 
	Config::getOrDefine('ENABLE_OPENID_AUTHENTICATION', false);
}

Config::getOrDefine('MAIL_SEND_METHOD', 'PHP_MAIL');

if (!defined('ENABLE_REGISTRATION_CAPTCHA')) { 
	Config::getOrDefine('ENABLE_REGISTRATION_CAPTCHA', true);
}

if (!defined('ENABLE_USER_PROFILES')) { 
	Config::getOrDefine('ENABLE_USER_PROFILES', false);
}

# If user registration with email address is true we don't use username's - we just use uEmail and we populate uName with the email address
if (!defined('USER_REGISTRATION_WITH_EMAIL_ADDRESS')) {
	Config::getOrDefine('USER_REGISTRATION_WITH_EMAIL_ADDRESS', false);
}

// allow spaces in usernames
if (!defined('USER_USERNAME_ALLOW_SPACES')) {
	Config::getOrDefine('USER_USERNAME_ALLOW_SPACES', false);	
}

if (!defined('USER_VALIDATE_EMAIL')) {
	Config::getOrDefine('USER_VALIDATE_EMAIL', false);	
}

if (!defined('USER_VALIDATE_EMAIL_REQUIRED')) {
	Config::getOrDefine('USER_VALIDATE_EMAIL_REQUIRED', false);	
}

if (!defined('USER_REGISTRATION_APPROVAL_REQUIRED')) {
	Config::getOrDefine('USER_REGISTRATION_APPROVAL_REQUIRED', false);
}

if (!defined('REGISTRATION_TYPE')) {
	Config::getOrDefine('REGISTRATION_TYPE', 'disabled');	
}

if (!defined('ENABLE_REGISTRATION')) {
	Config::getOrDefine('ENABLE_REGISTRATION', false);	
}

if (!defined('ENABLE_USER_TIMEZONES')) {
	Config::getOrDefine('ENABLE_USER_TIMEZONES', false);	
}

//these are the hashkey types for registration related authentication
define('UVTYPE_REGISTER', 0);
define('UVTYPE_CHANGE_PASSWORD', 1);


if (!defined('UPLOAD_FILE_EXTENSIONS_ALLOWED')) {
	Config::getOrDefine('UPLOAD_FILE_EXTENSIONS_ALLOWED','*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.mp4;*.mp3;*.qt;*.ppt;*.kml;*.xml');
	define('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', true);
} else {
	define('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', false);
}

define('BLOCK_NOT_AVAILABLE_TEXT', t('This block is no longer available.'));
define('GUEST_GROUP_NAME', t('Guest'));
define('REGISTERED_GROUP_NAME', t('Registered Users'));
define('ADMIN_GROUP_NAME', t('Admin'));