<?
defined('C5_EXECUTE') or die("Access Denied.");
if (!defined('DISPATCHER_FILENAME')) {
	define('DISPATCHER_FILENAME', 'index.php');
}
if (!defined('C5_ENVIRONMENT_ONLY')) {
	define('C5_ENVIRONMENT_ONLY', false);
}

if (!defined('ENABLE_CMS_FOR_DIRECTORY')) {
	define('ENABLE_CMS_FOR_DIRECTORY', true);
}

# These items should be set by site.php in config/ but if they're not that means we're installing and we need something there
/* https patch applied here */
if (!defined('BASE_URL')) { 
	if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
		define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST']);
	} else {
		define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
	}
}

if (!defined('DIR_REL')) {
	$pos = stripos($_SERVER['SCRIPT_NAME'], DISPATCHER_FILENAME);
	if($pos > 0) { //we do this because in CLI circumstances (and some random ones) we would end up with index.ph instead of index.php
		$pos = $pos - 1;
	}
	$uri = substr($_SERVER['SCRIPT_NAME'], 0, $pos);
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
	define("ENABLE_DEVELOPER_OPTIONS", true);
}

/*
if (!defined('ENABLE_OPENID_AUTHENTICATION')) { 
	define('ENABLE_OPENID_AUTHENTICATION', false);
}
*/
if (!defined('ENABLE_EMAILS')) {
	define('ENABLE_EMAILS', true);
}

if (!defined('EMAIL_DEFAULT_FROM_ADDRESS')) {
	define('EMAIL_DEFAULT_FROM_ADDRESS', 'concrete5-noreply@' . str_replace(array('http://www.', 'https://www.', 'http://', 'https://'), '', BASE_URL));
}

if (!defined('EMAIL_DEFAULT_FROM_NAME')) {
	define('EMAIL_DEFAULT_FROM_NAME', '');
}

if (!defined('SITEMAP_PAGES_LIMIT')) {
	define('SITEMAP_PAGES_LIMIT', 100);
}

if (!defined('ENABLE_DEFINABLE_USER_ATTRIBUTES')) {
	define('ENABLE_DEFINABLE_USER_ATTRIBUTES', true);
}

if (!defined('ENABLE_CUSTOM_USER_ATTRIBUTES_MODEL')) {
	define('ENABLE_CUSTOM_USER_ATTRIBUTES_MODEL', false);
}

if (!defined("PAGE_TITLE_FORMAT")) {
	define('PAGE_TITLE_FORMAT', '%1$s :: %2$s');
}

if (!defined("PAGE_PATH_SEPARATOR")) {
	define('PAGE_PATH_SEPARATOR', '-');
}

if (!defined('ENABLE_ASSET_COMPRESSION')) {
	define('ENABLE_ASSET_COMPRESSION', false);
}

if (!defined('PAGING_STRING')) {
	define('PAGING_STRING', 'ccm_paging_p');
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

define("LANGUAGE_DOMAIN_CORE", "messages");

# Path to the core files shared between all concrete 5 installations
if (!defined('DIR_BASE_CORE')) {
	define('DIR_BASE_CORE', realpath(dirname(__FILE__) . '/..'));
}

define('DIRNAME_CORE_CLASSES', 'core');
# if "concrete/" does NOT exist in DIR_BASE then we set multi_site to on
if (!is_dir(DIR_BASE . '/' . DIRNAME_APP)) {
	define("MULTI_SITE", 1);
}

# The core output buffering level. In the view class we need to know what the
# initial value is. Usually it's zero but sometimes PHP is setting this to one
# (gzip encoding?)
define('OB_INITIAL_LEVEL', ob_get_level());

# Used by the loader to load core libraries
define('DIR_LIBRARIES', DIR_BASE . '/libraries'); // front-end
define('DIR_LIBRARIES_CORE', DIR_BASE_CORE . '/libraries'); // front-end
define('DIR_LIBRARIES_3RDPARTY', DIR_LIBRARIES . '/3rdparty');
define('DIR_LIBRARIES_3RDPARTY_CORE', DIR_LIBRARIES_CORE . '/3rdparty');

ini_set('include_path', DIR_LIBRARIES_3RDPARTY . PATH_SEPARATOR . DIR_LIBRARIES_3RDPARTY_CORE . PATH_SEPARATOR . get_include_path());

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
if (!defined('DIR_PACKAGES')) {
	define('DIR_PACKAGES', DIR_BASE . '/packages');
}
define('DIR_PACKAGES_CORE', DIR_BASE_CORE . '/packages');
define('DIR_STARTING_POINT_PACKAGES', DIR_BASE . '/config/install/packages');
define('DIR_STARTING_POINT_PACKAGES_CORE', DIR_BASE_CORE . '/config/install/packages');

define('DIRNAME_BLOCKS', 'blocks');
define('DIRNAME_BACKUPS', 'backups');
define('DIRNAME_PAGES', 'single_pages');
define('DIRNAME_PACKAGES', 'packages');
define('DIRNAME_MODELS', 'models');
define('DIRNAME_ATTRIBUTES', 'attribute');
define('DIRNAME_ATTRIBUTE_TYPES', 'types');
define('DIRNAME_LIBRARIES', 'libraries');
define('DIRNAME_RESPONSE', 'response');
define('DIRNAME_PERMISSIONS', 'permission');
define('DIRNAME_WORKFLOW', 'workflow');
define('DIRNAME_WORKFLOW_ASSIGNMENTS', 'assignments');
define('DIRNAME_REQUESTS', 'requests');
define('DIRNAME_KEYS', 'keys');
define('DIRNAME_PAGE_TYPES', 'page_types');
define('DIRNAME_ELEMENTS', 'elements');
define('DIRNAME_LANGUAGES', 'languages');
define('DIRNAME_JOBS', 'jobs');
define('DIRNAME_DASHBOARD', 'dashboard');
define('DIRNAME_ELEMENTS_HEADER_MENU', 'header_menu');
define('DIRNAME_DASHBOARD_MODULES', 'modules');
define('DIRNAME_MAIL_TEMPLATES', 'mail');
define('DIRNAME_THEMES', 'themes');
if (!defined('DIRNAME_THEMES_CORE')) {
	define('DIRNAME_THEMES_CORE', 'core');
}
define('DIRNAME_TOOLS', 'tools');
define('DIRNAME_BLOCK_TOOLS', 'tools');
define('DIRNAME_BLOCK_TEMPLATES', 'templates');
define('DIRNAME_BLOCK_TEMPLATES_COMPOSER', 'composer');
define('DIRNAME_CSS', 'css');
define('DIRNAME_JAVASCRIPT', 'js');
define('DIRNAME_IMAGES', 'images');
define('DIRNAME_HELPERS', 'helpers');

define('DIRNAME_SYSTEM_TYPES', 'types');
define('DIRNAME_SYSTEM_CAPTCHA', 'captcha');
define('DIRNAME_SYSTEM_ANTISPAM', 'antispam');
define('DIRNAME_SYSTEM', 'system');

# Blocks
define('DIR_FILES_BLOCK_TYPES', DIR_BASE . '/blocks');
define('DIR_FILES_BLOCK_TYPES_CORE', DIR_BASE_CORE . '/blocks');
define('FILENAME_BLOCK_VIEW', 'view.php');
define('FILENAME_BLOCK_COMPOSER', 'composer.php');
define('FILENAME_BLOCK_VIEW_SCRAPBOOK', 'scrapbook.php');
define('FILENAME_BLOCK_ADD', 'add.php');
define('FILENAME_BLOCK_EDIT', 'edit.php');
define('FILENAME_BLOCK_ICON', 'icon.png');
define('FILENAME_BLOCK_CONTROLLER', 'controller.php');
define('FILENAME_BLOCK_DB', 'db.xml');
define('BLOCK_HANDLE_SCRAPBOOK_PROXY', 'core_scrapbook_display');
define('FILENAME_FORM', 'form.php');

# Stacks
define('STACKS_PAGE_PATH', '/!stacks');
define('STACKS_AREA_NAME', 'Main');
define('STACKS_PAGE_TYPE', 'core_stack');
define('BLOCK_HANDLE_STACK_PROXY', 'core_stack_display');

# Trash
define('TRASH_PAGE_PATH', '/!trash');

# Hosted assets are assets shared amongst all Concrete5 installations on a single machine.
if (defined('MULTI_SITE') && MULTI_SITE == 1) {
	define('ASSETS_URL_WEB', BASE_URL);
	@include(DIRNAME_UPDATES . '/index.php');
	if (isset($DIR_APP_UPDATES)) {
		define('DIR_APP_UPDATES', $DIR_APP_UPDATES);
	}
} else {
	define('DIR_APP_UPDATES', DIR_BASE . '/' . DIRNAME_UPDATES);
	define('ASSETS_URL_WEB', DIR_REL);
	define('MULTI_SITE', 0);
}
if (defined('DIRNAME_APP_UPDATED')) {
 	$ap = ASSETS_URL_WEB . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_APP;
} else {
	$ap = ASSETS_URL_WEB . '/' . DIRNAME_APP;
}

define('ASSETS_URL', $ap);
define('ASSETS_URL_CSS', $ap . '/css');
define('ASSETS_URL_JAVASCRIPT', $ap . '/js');
define('ASSETS_URL_IMAGES', $ap . '/images');
define('ASSETS_URL_FLASH', $ap . '/flash');

define('REL_DIR_STARTING_POINT_PACKAGES', DIR_REL . '/config/install/packages');
define('REL_DIR_STARTING_POINT_PACKAGES_CORE', ASSETS_URL . '/config/install/packages');
define('REL_DIR_PACKAGES', DIR_REL . '/packages');
define('REL_DIR_PACKAGES_CORE', ASSETS_URL . '/packages');


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
define('PACKAGE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_package_generic.png');

# Controllers
define('DIR_FILES_CONTROLLERS', DIR_BASE . '/controllers');
define('FILENAME_COLLECTION_CONTROLLER', 'controller.php');
define('DIRNAME_CONTROLLERS', 'controllers');
define('DIR_FILES_CONTROLLERS_REQUIRED', DIR_BASE_CORE . '/controllers');
define('FILENAME_ATTRIBUTE_CONTROLLER', 'controller.php');
define('FILENAME_ATTRIBUTE_DB', 'db.xml');
define('FILENAME_DB', 'db.xml');

# Elements
define('DIR_FILES_ELEMENTS', DIR_BASE . '/elements');
define('DIR_FILES_ELEMENTS_CORE', DIR_BASE_CORE . '/elements');
define('FILENAME_MENU_ITEM_CONTROLLER', 'controller.php');
define('FILENAME_CONTROLLER', 'controller.php');

# Jobs
if (!defined('DIR_FILES_JOBS')) {
	define('DIR_FILES_JOBS', DIR_BASE . '/jobs');
}
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

# languages
define('DIR_LANGUAGES', DIR_BASE . '/' . DIRNAME_LANGUAGES);
define('DIR_LANGUAGES_CORE', DIR_BASE_CORE . '/' . DIRNAME_LANGUAGES);

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

if (!defined('DIR_FILES_BACKUPS')) {
	define('DIR_FILES_BACKUPS', DIR_BASE . '/files/backups');
}
define('REL_DIR_FILES_UPLOADED_THUMBNAILS', DIR_REL . '/files/thumbnails');
define('REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_REL . '/files/thumbnails/level2');
define('REL_DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_REL . '/files/thumbnails/level3');
define('REL_DIR_FILES_CACHE', REL_DIR_FILES_UPLOADED . '/cache');

#Cache
if (!defined('DIR_FILES_CACHE')) {
	define('DIR_FILES_CACHE', DIR_BASE . '/files/cache');
}

if (!defined('CACHE_ID')) {
	define('CACHE_ID', md5(str_replace(array('https://', 'http://'), '', BASE_URL) . DIR_REL));
}

define('DISPATCHER_FILENAME_CORE', 'dispatcher.php');


if (defined('DIR_FILES_CACHE')) {
	define('DIR_FILES_CACHE_DB', DIR_FILES_CACHE);
	define('DIR_FILES_CACHE_PAGES', DIR_FILES_CACHE . '/lucene.pages');
	$ADODB_ACTIVE_CACHESECS = 300;
	$ADODB_CACHE_DIR = DIR_FILES_CACHE_DB;
}

if (!defined('CACHE_LIFETIME')) {
	define('CACHE_LIFETIME', null);
}

define('ON_WINDOWS', intval(substr(PHP_OS,0,3)=='WIN') );

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

if (!defined('AL_THUMBNAIL_JPEG_COMPRESSION')){ 
	define('AL_THUMBNAIL_JPEG_COMPRESSION', 80); 
}

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
define('NEWSFLOW_VIEWED_THRESHOLD', 86400); // once a day

# Default search size
define('SEARCH_CHUNK_SIZE','20'); /* number of entries retrieved per page */
if (!defined('PAGE_SEARCH_INDEX_LIFETIME')) {
	define('PAGE_SEARCH_INDEX_LIFETIME', 259200);
}
if (!defined('PAGE_SEARCH_INDEX_BATCH_SIZE')) {
	define('PAGE_SEARCH_INDEX_BATCH_SIZE', 200);
}

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

# Composer settings
define('COMPOSER_DRAFTS_PAGE_PATH', '/!drafts');

# User avatar constants - should probably be moved into the avatar helper class as avatar constants
if (!defined('AVATAR_WIDTH') && !defined('AVATAR_HEIGHT')) {
	define('AVATAR_WIDTH', 80);
	define('AVATAR_HEIGHT', 80);
}

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
	// caching now handled by our app, no longer by adodb
	define('DB_USE_CACHE', false);
}

if (!defined("API_KEY_PICNIK")) {
	define('API_KEY_PICNIK', '184f46c36757c7f060ed319eaf7337ac-' . urlencode(BASE_URL . DIR_REL . '/'));
}

$ADODB_ASSOC_CASE =  2;

require(dirname(__FILE__) . '/version.php');
define('APP_VERSION', $APP_VERSION);
define('APP_VERSION_LATEST_THRESHOLD', 172800); // Every 2 days we check for the latest version (this is seconds)
define('APP_VERSION_LATEST_WS', 'http://www.concrete5.org/tools/get_latest_version_number');
define('APP_VERSION_LATEST_DOWNLOAD', 'http://www.concrete5.org/download/');

//Main Concrete Site - For Marketplace, Knowledge Base, etc.
if (!defined('CONCRETE5_ORG_URL')) {
	define('CONCRETE5_ORG_URL', 'http://www.concrete5.org');
}
if (!defined('CONCRETE5_ORG_URL_SECURE')) {
	define('CONCRETE5_ORG_URL_SECURE', 'https://www.concrete5.org');
}

if (!defined('NEWSFLOW_URL')) {
	define('NEWSFLOW_URL', 'http://newsflow.concrete5.org');
}

if (!defined('ENABLE_TRASH_CAN')) { 
	define('ENABLE_TRASH_CAN', true);
}

define('MARKETPLACE_BASE_URL_SITE_PAGE', CONCRETE5_ORG_URL.'/private/sites');
define('NEWSFLOW_SLOT_CONTENT_URL', NEWSFLOW_URL . '/tools/slot_content/');

define('MARKETPLACE_URL_CONNECT', CONCRETE5_ORG_URL.'/marketplace/connect');
define('MARKETPLACE_URL_CONNECT_SUCCESS', CONCRETE5_ORG_URL.'/marketplace/connect/-/connected');
define('MARKETPLACE_URL_CHECKOUT', CONCRETE5_ORG_URL_SECURE.'/cart/-/add/');
define('MARKETPLACE_URL_CONNECT_VALIDATE', CONCRETE5_ORG_URL.'/marketplace/connect/-/validate');
define('MARKETPLACE_PURCHASES_LIST_WS', CONCRETE5_ORG_URL . '/marketplace/connect/-/get_available_licenses');
define('MARKETPLACE_ITEM_INFORMATION_WS', CONCRETE5_ORG_URL . '/marketplace/connect/-/get_item_information');
define('MARKETPLACE_ITEM_FREE_LICENSE_WS', CONCRETE5_ORG_URL . '/marketplace/connect/-/enable_free_license');
define('MARKETPLACE_URL_CONNECT_TOKEN_NEW', CONCRETE5_ORG_URL.'/marketplace/connect/-/generate_token');
define('MARKETPLACE_REMOTE_ITEM_LIST_WS', CONCRETE5_ORG_URL.'/marketplace/');

define('DASHBOARD_BACKGROUND_FEED', 'http://backgroundimages.concrete5.org/wallpaper');
if (!defined('DASHBOARD_BACKGROUND_INFO')) { 
	define('DASHBOARD_BACKGROUND_INFO', 'http://backgroundimages.concrete5.org/get_image_data.php');
}

if (!defined("MENU_HELP_URL")) {
	define('MENU_HELP_URL', CONCRETE5_ORG_URL . '/tools/help_overlay/');
}

if (!defined('MENU_HELP_SERVICE_URL')) {
	define('MENU_HELP_SERVICE_URL', CONCRETE5_ORG_URL . '/tools/get_remote_help_list/');
}

if (!defined('MARKETPLACE_THEME_PREVIEW_URL')) {
	define('MARKETPLACE_THEME_PREVIEW_URL', CONCRETE5_ORG_URL.'/tools/preview_theme/');
}

define('MARKETPLACE_CONTENT_LATEST_THRESHOLD', 10800); // every three hours
define('MARKETPLACE_DIRNAME_THEME_PREVIEW', 'previewable_themes');
define('MARKETPLACE_THEME_PREVIEW_ASSETS_URL', CONCRETE5_ORG_URL ."/". MARKETPLACE_DIRNAME_THEME_PREVIEW);

if(!defined('SITEMAPXML_FILE')) {
	/** The path (relative to the web root) of the sitemap.xml file to save [default value: 'sitemap.xml'].
	* @var string
	*/
	define('SITEMAPXML_FILE', 'sitemap.xml');
}
if(!defined('SITEMAPXML_DEFAULT_CHANGEFREQ')) {
	/** The default page change frequency [default value: 'weekly'; valid values: 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'].
	* @var string
	*/
	define('SITEMAPXML_DEFAULT_CHANGEFREQ', 'weekly');
}
if(!defined('SITEMAPXML_DEFAULT_PRIORITY')) {
	/** The default page priority [default value: 0.5; valid values from 0.0 to 1.0].
	* @var float
	*/
	define('SITEMAPXML_DEFAULT_PRIORITY', 0.5);
}