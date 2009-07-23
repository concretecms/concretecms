<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
define('DISPATCHER_FILENAME', 'index.php');
if (!defined('C5_ENVIRONMENT_ONLY')) {
	define('C5_ENVIRONMENT_ONLY', false);
}

# These items should be set by site.php in config/ but if they're not that means we're installing and we need something there
if (!defined('BASE_URL')) {
	define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
}

if (!defined('DIR_REL')) {
	$uri = substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], DISPATCHER_FILENAME) - 1);
	define('DIR_REL', $uri);
}

if ($config_check_failed) {
	// we define URL_REWRITING to be false
	define('URL_REWRITING', false);
}

// AS: moved to concrete/config/app.php Wednesday; February 4, 2009
// if (!defined('UPLOAD_FILE_EXTENSIONS_ALLOWED')) {
// 	define('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.avi;*.mp4;*.mp3;*.qt;*.ppt;*.kml');
// }

if (!defined('REDIRECT_TO_BASE_URL')) {
	define('REDIRECT_TO_BASE_URL', true);
}

if (!defined('ENABLE_DEVELOPER_OPTIONS')) {
	define("ENABLE_DEVELOPER_OPTIONS", false);
}

/*
if (!defined('ENABLE_OPENID_AUTHENTICATION')) { 
	define('ENABLE_OPENID_AUTHENTICATION', false);
}
*/
if (!defined('ENABLE_EMAILS')) {
	define('ENABLE_EMAILS', true);
}

if (!defined('ENABLE_DEFINABLE_USER_ATTRIBUTES')) {
	define('ENABLE_DEFINABLE_USER_ATTRIBUTES', true);
}

if (!defined('ENABLE_CUSTOM_USER_ATTRIBUTES_MODEL')) {
	define('ENABLE_CUSTOM_USER_ATTRIBUTES_MODEL', false);
}

if (!defined('STATISTICS_TRACK_PAGE_VIEWS')) {
	define('STATISTICS_TRACK_PAGE_VIEWS', true);
}

if (!defined("PAGE_TITLE_FORMAT")) {
	define('PAGE_TITLE_FORMAT', '%1$s :: %2$s');
}

if (!defined('ENABLE_ASSET_COMPRESSION')) {
	define('ENABLE_ASSET_COMPRESSION', false);
}

/** 
 * Character support
 */

if (!defined('APP_CHARSET')) {
	define('APP_CHARSET', 'UTF-8'); // pages, etc...
}

if (!defined('DB_CHARSET')) {
	define('DB_CHARSET', 'utf8'); // pages, etc...
}

if (!defined("DB_COLLATE")) {
	define('DB_COLLATE', '');
}

if (!defined('LOCALE')) {
	define("LOCALE", 'en_US');
}

if (strpos(LOCALE, '_') > -1) {
	$loc = explode('_', LOCALE);
	if (is_array($loc) && count($loc) == 2) {
		define('LANGUAGE', $loc[0]);
	}
}

if (!defined("LANGUAGE")) {
	define("LANGUAGE", LOCALE);
}

define("LANGUAGE_DOMAIN_CORE", "messages");

if (!defined('CACHE_LIBRARY')) {
	define('CACHE_LIBRARY', 'default');
}

# Debug Menu - Determines whether a "Submit Feedback/Bug/Question" is active */
# Currently Concrete5 does not include this capability but it will likely come back.
define('MENU_FEEDBACK_DISPLAY', 1);
define('MENU_FEEDBACK_URL', 'http://www.concretecms.com/tools/process_feedback.php');
if (!defined("MENU_HELP_URL")) {
	define('MENU_HELP_URL', 'http://www.concrete5.org/help/');
}

# Path to the core files shared between all concrete 5 installations
define('DIR_BASE_CORE', dirname(__FILE__) . '/..');

# Path to the base directory of THIS install
if (!defined('DIR_BASE')) {
	define('DIR_BASE', dirname($_SERVER['SCRIPT_FILENAME']));
}

# The core concrete directory. Either one per install or one per server
define('DIRNAME_APP', 'concrete');

# if "concrete/" does NOT exist in DIR_BASE then we set multi_site to on
if (!is_dir(DIR_BASE . '/' . DIRNAME_APP)) {
	define("MULTI_SITE", 1);
}

# The core output buffering level. In the view class we need to know what the
# initial value is. Usually it's zero but sometimes PHP is setting this to one
# (gzip encoding?)
define('OB_INITIAL_LEVEL', ob_get_level());

# Sessions/TMP directories
define('DIR_SESSIONS', '/tmp');
define('DISPATCHER_FILENAME_CORE', 'dispatcher.php');

# Used by the loader to load core libraries
define('DIR_LIBRARIES', DIR_BASE . '/libraries'); // front-end
define('DIR_LIBRARIES_CORE', DIR_BASE_CORE . '/libraries'); // front-end
define('DIR_LIBRARIES_3RDPARTY', DIR_LIBRARIES . '/3rdparty');
define('DIR_LIBRARIES_3RDPARTY_CORE', DIR_LIBRARIES_CORE . '/3rdparty');

ini_set('include_path', get_include_path() . PATH_SEPARATOR . DIR_LIBRARIES_3RDPARTY . PATH_SEPARATOR . DIR_LIBRARIES_3RDPARTY_CORE);

# Models are explicit things - concrete-related or not - that deal with the db
define('DIR_MODELS', DIR_BASE . '/models'); // front-end
define('DIR_MODELS_CORE', DIR_BASE_CORE . '/models'); // front-end

# Helpers are helper functions (duh)
define('DIR_HELPERS', DIR_BASE . '/helpers'); // front-end
define('DIR_HELPERS_CORE', DIR_BASE_CORE . '/helpers'); // front-end

# Tools are items that are wrapped in Concrete, they have db access, library support
# But they're really one-off scripts.
define('DIR_FILES_TOOLS', DIR_BASE . '/tools'); // front-end
define('DIR_FILES_TOOLS_REQUIRED', DIR_BASE_CORE . '/tools'); // global

# Packages 
define('DIR_PACKAGES', DIR_BASE . '/packages');
define('DIR_PACKAGES_CORE', DIR_BASE_CORE . '/packages');
define('DIRNAME_PACKAGE_CORE', 'core');
define('DIR_PACKAGE_CORE', DIR_BASE_CORE . '/packages/' . DIRNAME_PACKAGE_CORE);

define('DIRNAME_BLOCKS', 'blocks');
define('DIRNAME_PAGES', 'single_pages');
define('DIRNAME_PACKAGES', 'packages');
define('DIRNAME_MODELS', 'models');
define('DIRNAME_LIBRARIES', 'libraries');
define('DIRNAME_PAGE_TYPES', 'page_types');
define('DIRNAME_ELEMENTS', 'elements');
define('DIRNAME_LANGUAGES', 'languages');
define('DIRNAME_JOBS', 'jobs');
define('DIRNAME_DASHBOARD', 'dashboard');
define('DIRNAME_DASHBOARD_MODULES', 'modules');
define('DIRNAME_MAIL_TEMPLATES', 'mail');
define('DIRNAME_THEMES', 'themes');
if (!defined('DIRNAME_THEMES_CORE')) {
	define('DIRNAME_THEMES_CORE', 'core');
}
define('DIRNAME_TOOLS', 'tools');
define('DIRNAME_BLOCK_TOOLS', 'tools');
define('DIRNAME_BLOCK_TEMPLATES', 'templates');
define('DIRNAME_CSS', 'css');
define('DIRNAME_JAVASCRIPT', 'js');
define('DIRNAME_IMAGES', 'images');
define('DIRNAME_HELPERS', 'helpers');

# Blocks
define('DIR_FILES_BLOCK_TYPES', DIR_BASE . '/blocks');
define('DIR_FILES_BLOCK_TYPES_CORE', DIR_BASE_CORE . '/blocks');
define('FILENAME_BLOCK_VIEW', 'view.php');
define('FILENAME_BLOCK_VIEW_SCRAPBOOK', 'scrapbook.php');
define('FILENAME_BLOCK_ADD', 'add.php');
define('FILENAME_BLOCK_EDIT', 'edit.php');
define('FILENAME_BLOCK_ICON', 'icon.png');
define('FILENAME_BLOCK_CONTROLLER', 'controller.php');
define('FILENAME_BLOCK_DB', 'db.xml');

# Hosted assets are assets shared amongst all Concrete5 installations on a single machine.
if (defined('MULTI_SITE') && MULTI_SITE == 1) {
	define('ASSETS_URL_WEB', BASE_URL);
} else {
	define('ASSETS_URL_WEB', DIR_REL);
	define('MULTI_SITE', 0);
}

define('ASSETS_URL', ASSETS_URL_WEB . '/concrete');
define('ASSETS_URL_CSS', ASSETS_URL_WEB . '/concrete/css');
define('ASSETS_URL_JAVASCRIPT', ASSETS_URL_WEB . '/concrete/js');
define('ASSETS_URL_IMAGES', ASSETS_URL_WEB . '/concrete/images');
define('ASSETS_URL_FLASH', ASSETS_URL_WEB . '/concrete/flash');

# Pages/Collections
define('FILENAME_COLLECTION_VIEW', 'view.php');
define('FILENAME_COLLECTION_ACCESS', 'access.xml');
define('FILENAME_COLLECTION_EDIT', 'edit.php');
define('FILENAME_COLLECTION_DEFAULT_THEME', 'default');
define('FILENAME_COLLECTION_TYPE_DEFAULT_ICON', 'main.png');
define('FILENAME_PAGE_ICON', 'icon.png');
define('FILENAME_PACKAGE_CONTROLLER', 'controller.php');
define('FILENAME_PACKAGE_DB', 'db.xml');
//define('DIR_FILES_COLLECTION_TYPES', DIR_BASE . '/views/page_types');
define('DIR_FILES_COLLECTION_TYPE_ICONS', DIR_BASE_CORE . '/images/icons/page_types');
define('REL_DIR_FILES_COLLECTION_TYPE_ICONS', ASSETS_URL_IMAGES . '/icons/page_types');
define('COLLECTION_TYPE_ICON_WIDTH', 120);
define('COLLECTION_TYPE_ICON_HEIGHT', 90);
define('DIR_FILES_CONTENT', DIR_BASE . '/single_pages');
define('DIR_FILES_CONTENT_REQUIRED', DIR_BASE_CORE . '/single_pages');
define("FILENAME_LOCAL_DB", 'site_db.xml');

# Block Types
define('BLOCK_TYPE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_block_type_generic.png');

# Controllers
define('DIR_FILES_CONTROLLERS', DIR_BASE . '/controllers');
define('FILENAME_COLLECTION_CONTROLLER', 'controller.php');
define('DIRNAME_CONTROLLERS', 'controllers');
define('DIR_FILES_CONTROLLERS_REQUIRED', DIR_BASE_CORE . '/controllers');

# Elements
define('DIR_FILES_ELEMENTS', DIR_BASE . '/elements');
define('DIR_FILES_ELEMENTS_CORE', DIR_BASE_CORE . '/elements');

# Jobs
define('DIR_FILES_JOBS', DIR_BASE . '/jobs');
define('DIR_FILES_JOBS_CORE', DIR_BASE_CORE . '/jobs');

# Themes
define('VIEW_CORE_THEME', 'concrete');
define('DIR_FILES_THEMES', DIR_BASE . '/themes');
define('DIR_FILES_THEMES_CORE', DIR_BASE_CORE . '/themes');
define('DIR_FILES_THEMES_CORE_ADMIN', DIR_BASE_CORE . '/themes/core');
define('FILENAME_THEMES_DESCRIPTION', 'description.txt');
define('FILENAME_THEMES_DEFAULT', 'default.php');
define('FILENAME_THEMES_VIEW', 'view.php');
define('FILENAME_THEMES_THUMBNAIL', 'thumbnail.png');
define('FILENAME_THEMES_ERROR', 'error');
define('ASSETS_URL_THEMES_NO_THUMBNAIL', ASSETS_URL_IMAGES . '/spacer.gif');
define('THEMES_THUMBNAIL_WIDTH', 120);
define('THEMES_THUMBNAIL_HEIGHT', 90);

# Mail templates are just another kind of element, but with some special properties
define('DIR_FILES_EMAIL_TEMPLATES', DIR_BASE . '/mail');
define('DIR_FILES_EMAIL_TEMPLATES_CORE', DIR_BASE_CORE . '/mail');

# Items used by the custom form core block
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL', DIR_FILES_BLOCK_TYPES . '/external_form/forms/');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS', DIR_FILES_BLOCK_TYPES . '/external_form/forms/controllers');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/forms');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/forms/controllers');

define('DIR_FILES_UPLOADED_STANDARD', DIR_BASE . '/files');
define('DIR_FILES_TRASH_STANDARD', DIR_BASE . '/files/trash');
define('REL_DIR_FILES_UPLOADED', DIR_REL . '/files');

define('DIR_FILES_UPLOADED_THUMBNAILS', DIR_BASE . '/files/thumbnails');
define('REL_DIR_FILES_UPLOADED_THUMBNAILS', DIR_REL . '/files/thumbnails');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_BASE . '/files/thumbnails/level2');
define('REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_REL . '/files/thumbnails/level2');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_BASE . '/files/thumbnails/level3');
define('REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_REL . '/files/thumbnails/level3');
define('REL_DIR_FILES_CACHE', REL_DIR_FILES_UPLOADED . '/cache');

#Cache
define('DIR_FILES_CACHE', DIR_BASE . '/files/cache');
if (!is_dir(DIR_FILES_CACHE)) {
	@mkdir(DIR_FILES_CACHE);
	@chmod(DIR_FILES_CACHE, 0777);
}

define('DIR_FILES_CACHE_DB', DIR_FILES_CACHE);
define('DIR_FILES_CACHE_CORE', DIR_BASE . '/files/cache_objects');
define('DIR_FILES_CACHE_PAGES', DIR_FILES_CACHE . '/lucene.pages');
define('ON_WINDOWS', intval(substr(PHP_OS,0,3)=='WIN') );

# Binaries used by the system
# Currently unused
# define('DIR_FILES_BIN', DIR_BASE_CORE . '/bin');
define('DIR_FILES_BIN_HTMLDIFF', DIR_LIBRARIES_3RDPARTY_CORE . '/htmldiff.py');
if (!defined('DIR_FILES_BIN_UNZIP')) {
	 define('DIR_FILES_BIN_UNZIP', '/usr/bin/unzip');
}
define('DIR_FILES_BIN_COMPRESS_ASSETS', DIR_LIBRARIES_3RDPARTY_CORE . '/minify_2.1.2/index.php');

if (!defined('DIR_FILES_BIN_ZIP')) {
	 define('DIR_FILES_BIN_ZIP', '/usr/bin/zip');
}
if(!defined('DIR_FILES_BIN_ASPELL')) define('DIR_FILES_BIN_ASPELL', '/usr/bin/aspell'); // spellchecker

# Asset library constants 
define('AL_THUMBNAIL_WIDTH', '60');
define('AL_THUMBNAIL_HEIGHT', '60');
define('AL_THUMBNAIL_WIDTH_LEVEL1', '60'); // level1 duplicated here for internal functions
define('AL_THUMBNAIL_HEIGHT_LEVEL1', '60');
define('AL_THUMBNAIL_WIDTH_LEVEL2', '250');
define('AL_THUMBNAIL_HEIGHT_LEVEL2', '250');

define('AL_ICON_WIDTH', 24);
define('AL_ICON_HEIGHT', 24);
define('DIR_AL_ICONS', DIR_BASE_CORE . '/images/icons/filetypes');
define('REL_DIR_AL_ICONS', ASSETS_URL_IMAGES . '/icons/filetypes');
define('AL_ICON_DEFAULT', ASSETS_URL_IMAGES . '/icons/filetypes/default.png');

# This is the max size of any image in the system
define('IMAGE_MAX_WIDTH','1200'); // this is the max - can't be any higher, this overrides area settings
define('IMAGE_MAX_HEIGHT','1200');

# User constants
define('USER_USERNAME_MINIMUM', 3);
define('USER_PASSWORD_MINIMUM', 5);
define('USER_USERNAME_MAXIMUM', 64);
define('USER_PASSWORD_MAXIMUM', 64);
define('USER_SUPER', 'admin');
define('USER_SUPER_ID', 1);
define('GUEST_GROUP_ID', '1');
define('REGISTERED_GROUP_ID', '2');
define('ADMIN_GROUP_ID', '3');
define('SESSION_MAX_LIFETIME', 7200); // 2 hours
define('USER_CHANGE_PASSWORD_URL_LIFETIME',  7200);

# Default search size
define('SEARCH_CHUNK_SIZE','20'); /* number of entries retrieved per page */

# Versioning/Editing defaults 
define('CHECKOUT_TIMEOUT', 300); // # in seconds.
define('VERSION_INITIAL_COMMENT', 'Initial Version');
define('ONLINE_NOW_TIMEOUT', 300);

# Information for the home page in the system (used by the installation program)
define("HOME_CID", 1);
define("HOME_CTID", 1);
define("HOME_NAME", "Home");
define('HOME_UID', USER_SUPER_ID);
define('HOME_HANDLE', "home");

# User avatar constants - should probably be moved into the avatar helper class as avatar constants
if (!defined('AVATAR_WIDTH') && !defined('AVATAR_HEIGHT')) {
	define('AVATAR_WIDTH', 80);
	define('AVATAR_HEIGHT', 80);
}

define('DIR_FILES_AVATARS', DIR_BASE . '/files/avatars');
define('REL_DIR_FILES_AVATARS', REL_DIR_FILES_UPLOADED . '/avatars');
if (!defined('AVATAR_NONE')) {
	define('AVATAR_NONE', ASSETS_URL_IMAGES . '/spacer.gif');
}
define('REL_DIR_FILES_AVATARS_STOCK', REL_DIR_FILES_UPLOADED . '/stock_avatars');

# CMS errors - this is legacy
define('COLLECTION_NOT_FOUND', 10);
define('COLLECTION_INIT', 11);
define('COLLECTION_FORBIDDEN', 12);
define('VERSION_NOT_RECENT', 50);
define('USER_INVALID', 20);
define('USER_INACTIVE', 21);
define('USER_NON_VALIDATED', 22);
define('COLLECTION_MASTER_UNAUTH', 30);
define('COLLECTION_PRIVATE', 40);
define('BLOCK_NOT_AVAILABLE', 50);

# Debug and Logging
define('DEBUG_DISPLAY_PRODUCTION', 0);
define('DEBUG_DISPLAY_ERRORS', 1);
define('DEBUG_DISPLAY_ERRORS_SQL', 2); // not used
define('DIRNAME_LOGS', 'logs'); // logs archive
define('LOG_TYPE_EMAILS', 'sent_emails');
define('LOG_TYPE_EXCEPTIONS', 'exceptions');

# The name of the session cookie used.
if (!defined('SESSION')) {
	define('SESSION', 'CONCRETE5');
}

# Variables/constants necessary for ADODB
define('DB_TYPE', 'mysql');
if (!defined('DB_USE_CACHE')) {
	define('DB_USE_CACHE', true);
}

if (!defined("API_KEY_PICNIK")) {
	define('API_KEY_PICNIK', '184f46c36757c7f060ed319eaf7337ac-' . urlencode(BASE_URL . DIR_REL . '/'));
}

$ADODB_ASSOC_CASE =  2;
$ADODB_ACTIVE_CACHESECS = 300;
$ADODB_CACHE_DIR = DIR_FILES_CACHE_DB;
define('APP_VERSION', '5.3.2');
define('APP_VERSION_LATEST_THRESHOLD', 172800); // Every 2 days we check for the latest version (this is seconds)
define('APP_VERSION_LATEST_WS', 'http://www.concrete5.org/tools/get_latest_version_number');
define('APP_VERSION_LATEST_DOWNLOAD', 'http://www.concrete5.org/download/');

//Main Concrete Site - For Marketplace, Knowledge Base, etc.
if (!defined('CONCRETE5_ORG_URL')) {
	define('CONCRETE5_ORG_URL', 'http://www.concrete5.org');
}

# Marketplace Vars
if (!defined("MARKETPLACE_URL_LANDING")) {
	define('MARKETPLACE_URL_LANDING', CONCRETE5_ORG_URL.'/marketplace/');
}
if (!defined('MARKETPLACE_BLOCK_LIST_WS')) {
	define('MARKETPLACE_BLOCK_LIST_WS', CONCRETE5_ORG_URL.'/marketplace/addons/-/get_remote_list');
}
if (!defined('MARKETPLACE_THEME_LIST_WS')) {
	define('MARKETPLACE_THEME_LIST_WS', CONCRETE5_ORG_URL.'/marketplace/themes/-/get_remote_list/');
}
if (!defined('MARKETPLACE_THEME_PREVIEW_URL')) {
	define('MARKETPLACE_THEME_PREVIEW_URL', CONCRETE5_ORG_URL.'/tools/preview_theme/');
}
if (!defined('MARKETPLACE_PURCHASES_LIST_WS')) {
	define('MARKETPLACE_PURCHASES_LIST_WS', CONCRETE5_ORG_URL.'/tools/get_purchased_block_list/');
}
define('MARKETPLACE_CONTENT_LATEST_THRESHOLD', 10800); // every three hours
define('MARKETPLACE_DIRNAME_THEME_PREVIEW', 'previewable_themes');
define('MARKETPLACE_THEME_PREVIEW_ASSETS_URL', CONCRETE5_ORG_URL ."/". MARKETPLACE_DIRNAME_THEME_PREVIEW);

# Knowledge Base Vars
if (!defined('KNOWLEDGE_BASE_URL')){
	define('KNOWLEDGE_BASE_URL', CONCRETE5_ORG_URL.'/help/kb/');
}
if (!defined('KNOWLEDGE_BASE_POST_URL')) {
	define('KNOWLEDGE_BASE_POST_URL', CONCRETE5_ORG_URL.'/tools/open_support_ticket/');
}
if (!defined('KNOWLEDGE_BASE_TICKET_LIST_URL')) {
	define('KNOWLEDGE_BASE_TICKET_LIST_URL', CONCRETE5_ORG_URL.'/tools/get_support_ticket_list/');
}
if (!defined('KNOWLEDGE_BASE_AUTH_URL')) {
	define('KNOWLEDGE_BASE_AUTH_URL', CONCRETE5_ORG_URL.'/tools/authenticate_user/');
}
if (!defined('KNOWLEDGE_BASE_SUPPORT_LEARN_MORE_URL')) {
	define('KNOWLEDGE_BASE_SUPPORT_LEARN_MORE_URL', CONCRETE5_ORG_URL.'/support/owner_support/');
}

require_once(DIR_LIBRARIES_CORE . '/loader.php');
