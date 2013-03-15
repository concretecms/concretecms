<?
/**
 *
 * When this file is run it basically queries the database for site config items and sets those up, possibly overriding items in the base.php.
 * The hierarchy basically goes like this:
 * 1. Item defined in config/site.php? Then it will be used.
 * 2. Item saved in database? Then it will be used.
 * 3. Otherwise, we setup the defaults below.
 **/
defined('C5_EXECUTE') or die("Access Denied."); 

if (!defined('ENABLE_OVERRIDE_CACHE')) {
	Config::getOrDefine('ENABLE_OVERRIDE_CACHE', false); 
}

if (!defined('ENABLE_BLOCK_CACHE')) {
	Config::getOrDefine('ENABLE_BLOCK_CACHE', true); 
}

if (!defined('FULL_PAGE_CACHE_GLOBAL')) {
	Config::getOrDefine('FULL_PAGE_CACHE_GLOBAL', false);	
}

if (!defined('STATISTICS_TRACK_PAGE_VIEWS')) {
	Config::getOrDefine('STATISTICS_TRACK_PAGE_VIEWS', true);
}

Config::getOrDefine('FULL_PAGE_CACHE_LIFETIME', 'default');		

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

if (!defined('ENABLE_INTELLIGENT_SEARCH_HELP')) {
	Config::getOrDefine('ENABLE_INTELLIGENT_SEARCH_HELP', true);
}

if (!defined('ENABLE_INTELLIGENT_SEARCH_MARKETPLACE')) {
	if (!ENABLE_MARKETPLACE_SUPPORT) {
		define('ENABLE_INTELLIGENT_SEARCH_MARKETPLACE', false);
	} else { 
		Config::getOrDefine('ENABLE_INTELLIGENT_SEARCH_MARKETPLACE', true);
	}
}

if (!defined('ENABLE_NEWSFLOW_OVERLAY')) {
	Config::getOrDefine('ENABLE_NEWSFLOW_OVERLAY', true);
}

if (!defined('WHITE_LABEL_LOGO_SRC')) {
	Config::getOrDefine('WHITE_LABEL_LOGO_SRC', false);
}

if (!defined('WHITE_LABEL_APP_NAME')) {
	Config::getOrDefine('WHITE_LABEL_APP_NAME', false);
}

if (!defined("ENABLE_AREA_LAYOUTS")) {
	Config::getOrDefine('ENABLE_AREA_LAYOUTS', true);
}

if (!defined("ENABLE_CUSTOM_DESIGN")) {
	Config::getOrDefine('ENABLE_CUSTOM_DESIGN', true);
}

if (!defined('URL_REWRITING_ALL')) { 
	define("URL_REWRITING_ALL", false);
}

if (!defined('ENABLE_LEGACY_CONTROLLER_URLS')) {
	define('ENABLE_LEGACY_CONTROLLER_URLS', false);
}

if (!defined('ENABLE_PROGRESSIVE_PAGE_REINDEX')) {
	define('ENABLE_PROGRESSIVE_PAGE_REINDEX', true);
}

if (!defined('ENABLE_APP_NEWS')) {
	Config::getOrDefine('ENABLE_APP_NEWS', true);
}

if (!defined('FORBIDDEN_SHOW_LOGIN')) {
	Config::getOrDefine('FORBIDDEN_SHOW_LOGIN', true); //show the login page instead of forbidden for non-logged in users
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

if (!defined('MAIL_SEND_METHOD')) { 
	Config::getOrDefine('MAIL_SEND_METHOD', 'PHP_MAIL');
}

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

if (!defined('REGISTER_NOTIFICATION')) {
	Config::getOrDefine('REGISTER_NOTIFICATION', false);
}

if (!defined('EMAIL_ADDRESS_REGISTER_NOTIFICATION')) {
	Config::getOrDefine('EMAIL_ADDRESS_REGISTER_NOTIFICATION', false);
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

// private message limitations
if(!defined('USER_PRIVATE_MESSAGE_MAX')) {
	Config::getOrDefine('USER_PRIVATE_MESSAGE_MAX', '20'); // number of messages that can be sent within USER_PRIVATE_MESSAGE_MAX_TIME_SPAN
}
if(!defined('USER_PRIVATE_MESSAGE_MAX_TIME_SPAN')) {
	Config::getOrDefine('USER_PRIVATE_MESSAGE_MAX_TIME_SPAN', '15'); // minutes
}

//these are the hashkey types for registration related authentication
define('UVTYPE_REGISTER', 0);
define('UVTYPE_CHANGE_PASSWORD', 1);


if (!defined('UPLOAD_FILE_EXTENSIONS_ALLOWED')) {
	Config::getOrDefine('UPLOAD_FILE_EXTENSIONS_ALLOWED','*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.xlsx;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.3gp;*.avi;*.m4v;*.mp4;*.mp3;*.qt;*.ppt;*.pptx;*.kml;*.xml;*.svg;*.webm;*.ogg;*.ogv');
	define('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', true);
} else {
	define('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', false);
}

if (!defined('SEO_EXCLUDE_WORDS')) {
	Config::getOrDefine('SEO_EXCLUDE_WORDS', 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, since, than, the, this, that, to, up, via, with');
}

if (!defined('ENABLE_JOB_SCHEDULING')) {
	Config::getOrDefine('ENABLE_JOB_SCHEDULING', true);
}