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

# File settings
if (!defined('DIR_FILES_UPLOADED')) {
	Config::getOrDefine('DIR_FILES_UPLOADED', DIR_BASE . '/files');
}

define('DIR_FILES_TRASH', DIR_FILES_UPLOADED . '/trash');
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

//these are the hashkey types for registration related authentication
define('UVTYPE_REGISTER', 0);
define('UVTYPE_CHANGE_PASSWORD', 1);


if (!defined('UPLOAD_FILE_EXTENSIONS_ALLOWED')) {
	Config::getOrDefine('UPLOAD_FILE_EXTENSIONS_ALLOWED','*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.mp4;*.mp3;*.qt;*.ppt;*.kml');
}

define('BLOCK_NOT_AVAILABLE_TEXT', t('This block is no longer available.'));
define('GUEST_GROUP_NAME', t('Guest'));
define('REGISTERED_GROUP_NAME', t('Registered Users'));
define('ADMIN_GROUP_NAME', t('Admin'));

# User & Registration Settings

if (!defined('ENABLE_OPENID_AUTHENTICATION')) { 
	Config::getOrDefine('ENABLE_OPENID_AUTHENTICATION', false);
}
if (!defined('ENABLE_USER_PROFILES')) { 
	Config::getOrDefine('ENABLE_USER_PROFILES', false);
}

# If user registration with email address is true we don't use username's - we just use uEmail and we populate uName with the email address
if (!defined('USER_REGISTRATION_WITH_EMAIL_ADDRESS')) {
	Config::getOrDefine('USER_REGISTRATION_WITH_EMAIL_ADDRESS', false);
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