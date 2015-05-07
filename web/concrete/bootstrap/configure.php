<?php

if (version_compare(phpversion(), '5.3.3') < 0) {
    die("concrete5 requires PHP 5.3.3+ to run.\n");
}

/**
 * ----------------------------------------------------------------------------
 * Ensure that all subsequent procedural pages are running inside concrete5.
 * ----------------------------------------------------------------------------
 */
defined('C5_EXECUTE') or define('C5_EXECUTE', md5(uniqid()));



/**
 * ----------------------------------------------------------------------------
 * The following constants need to load very early, because they're used
 * if we determine that we have an updated core, and they're also used to
 * determine where we grab our site config file from. So first we load them,
 * then we attempt to load the site config, then we pass through to an updated
 * core, should our site config point to that new core. Only then after that
 * do we continue loading this instance of concrete5.
 * ----------------------------------------------------------------------------
 */
defined('DISPATCHER_FILENAME') or define('DISPATCHER_FILENAME', 'index.php');
defined('DISPATCHER_FILENAME_CORE') or define('DISPATCHER_FILENAME_CORE', 'dispatcher.php');
defined('DIRNAME_APPLICATION') or define('DIRNAME_APPLICATION', 'application');
defined('DIRNAME_UPDATES') or define('DIRNAME_UPDATES', 'updates');
defined('DIRNAME_CORE') or define('DIRNAME_CORE', 'concrete');
defined('DIR_BASE') or define('DIR_BASE', dirname($_SERVER['SCRIPT_FILENAME']));
defined('DIR_APPLICATION') or define('DIR_APPLICATION', DIR_BASE . '/' . DIRNAME_APPLICATION);
defined('DIR_CONFIG_SITE') or define('DIR_CONFIG_SITE', DIR_APPLICATION . '/config');

/**
 * ----------------------------------------------------------------------------
 * Now that we've had the opportunity to load our config file, we know if we
 * have a DIRNAME_CORE_UPDATED constant, which lives in that file, and which
 * points to another core. If we have this constant, we exit this file
 * immeditely and proceed into the updated core.
 * ----------------------------------------------------------------------------
 */

$update_file = DIR_CONFIG_SITE . '/update.php';
$updates = array();
if (file_exists($update_file)) {
    $updates = (array) include $update_file;
}
if (!defined('APP_UPDATED_PASSTHRU') && isset($updates['core'])) {
    define('APP_UPDATED_PASSTHRU', true);
    if (is_dir(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . $updates['core'])) {
        require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . $updates['core'] . '/' . DIRNAME_CORE . '/' . 'dispatcher.php');
    } else if(file_exists(DIRNAME_UPDATES . '/' . $updates['core'] . '/' . DIRNAME_CORE . '/' . 'dispatcher.php')){
        require(DIRNAME_UPDATES . '/' . $updates['core'] . '/' . DIRNAME_CORE . '/' . 'dispatcher.php');
    } else {
        die(sprintf('Invalid "%s" defined. Please remove it from %s.','update.core', $update_file));
    }
    exit;
}



/**
 * ----------------------------------------------------------------------------
 * ## If we're still here, we're proceeding through this concrete directory,
 * and it's time to load the rest of our hard-coded configuration options –
 * the one we don't need a database to tell us about.
 *
 * Namespacing and Autoloading
 * ----------------------------------------------------------------------------
 */
define('NAMESPACE_SEGMENT_VENDOR', 'Concrete');



/**
 * ----------------------------------------------------------------------------
 * Directory names
 * ----------------------------------------------------------------------------
 */
define('DIRNAME_BLOCKS', 'blocks');
define('DIRNAME_BACKUPS', 'backups');
define('DIRNAME_PAGES', 'single_pages');
define('DIRNAME_VIEWS', 'views');
define('DIRNAME_PACKAGES', 'packages');
define('DIRNAME_MODELS', 'models');
define('DIRNAME_ATTRIBUTES', 'attributes');
define('DIRNAME_MENU_ITEMS', 'menu_items');
define('DIRNAME_AUTHENTICATION', 'authentication');
define('DIRNAME_LIBRARIES', 'libraries');
define('DIRNAME_RESPONSE', 'response');
define('DIRNAME_PERMISSIONS', 'permission');
define('DIRNAME_WORKFLOW', 'workflow');
define('DIRNAME_WORKFLOW_ASSIGNMENTS', 'assignments');
define('DIRNAME_REQUESTS', 'requests');
define('DIRNAME_KEYS', 'keys');
define('DIRNAME_PAGE_TYPES', 'page_types');
define('DIRNAME_PAGE_TEMPLATES', 'page_templates');
define('DIRNAME_PAGE_THEME', 'page_theme');
define('DIRNAME_PAGE_THEME_CUSTOM', 'custom');
define('DIRNAME_ELEMENTS', 'elements');
define('DIRNAME_LANGUAGES', 'languages');
define('DIRNAME_JOBS', 'jobs');
define('DIRNAME_DASHBOARD', 'dashboard');
define('DIRNAME_ELEMENTS_HEADER_MENU', 'header_menu');
define('DIRNAME_DASHBOARD_MODULES', 'modules');
define('DIRNAME_MAIL_TEMPLATES', 'mail');
define('DIRNAME_THEMES', 'themes');
define('DIRNAME_THEMES_CORE', 'core');
define('DIRNAME_TOOLS', 'tools');
define('DIRNAME_BLOCK_TOOLS', 'tools');
define('DIRNAME_BLOCK_TEMPLATES', 'templates');
define('DIRNAME_BLOCK_TEMPLATES_COMPOSER', 'composer');
define('DIRNAME_CSS', 'css');
define('DIRNAME_CLASSES', 'src');
define('DIRNAME_PREVIEW', 'preview');
define('DIRNAME_GROUP', 'group');
define('DIRNAME_GROUP_AUTOMATION', 'automation');
define('DIRNAME_JAVASCRIPT', 'js');
define('DIRNAME_IMAGES', 'images');
define('DIRNAME_IMAGES_LANGUAGES', 'countries');
define('DIRNAME_HELPERS', 'helpers');
define('DIRNAME_USER_POINTS', 'user_point');
define('DIRNAME_ACTIONS', 'actions');
define('DIRNAME_SYSTEM_TYPES', 'types');
define('DIRNAME_SYSTEM_CAPTCHA', 'captcha');
define('DIRNAME_SYSTEM_ANTISPAM', 'antispam');
define('DIRNAME_SYSTEM', 'system');
define('DIRNAME_PANELS', 'panels');
define('DIRNAME_CONTROLLERS', 'controllers');
define('DIRNAME_PAGE_CONTROLLERS', 'single_page');
define('DIRNAME_GATHERING', 'gathering');
define('DIRNAME_GATHERING_DATA_SOURCES', 'data_sources');
define('DIRNAME_GATHERING_ITEM_TEMPLATES', 'templates');
define('DIRNAME_COMPOSER', 'composer');
define('DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES', 'target_types');
define('DIRNAME_COMPOSER_ELEMENTS_CONTROLS', 'controls');
define('DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES_FORM', 'form');
define('DIRNAME_CONVERSATIONS', 'conversation');
define('DIRNAME_CONVERSATION_EDITOR', 'editor');
define('DIRNAME_VENDOR', 'vendor');
define('DIRNAME_LANGUAGES_SITE_INTERFACE', 'site');
define('DIRNAME_STYLE_CUSTOMIZER', 'style_customizer');
define('DIRNAME_STYLE_CUSTOMIZER_TYPES', 'types');
define('DIRNAME_STYLE_CUSTOMIZER_PRESETS', 'presets');
define('DIRNAME_FILE_STORAGE_LOCATION_TYPES', 'storage_location_types');
define('REL_DIR_FILES_INCOMING', '/incoming');
define('REL_DIR_FILES_THUMBNAILS', '/thumbnails');



/**
 * ----------------------------------------------------------------------------
 * File names
 * ----------------------------------------------------------------------------
 */
define('FILENAME_BLOCK_VIEW', 'view.php');
define('FILENAME_BLOCK_COMPOSER', 'composer.php');
define('FILENAME_BLOCK_VIEW_SCRAPBOOK', 'scrapbook.php');
define('FILENAME_BLOCK_ADD', 'add.php');
define('FILENAME_BLOCK_EDIT', 'edit.php');
define('FILENAME_BLOCK_ICON', 'icon.png');
define('FILENAME_BLOCK_CONTROLLER', 'controller.php');
define('FILENAME_BLOCK_DB', 'db.xml');
define('FILENAME_FORM', 'form.php');
define('FILENAME_COLLECTION_VIEW', 'view.php');
define('FILENAME_COLLECTION_ACCESS', 'access.xml');
define('FILENAME_COLLECTION_EDIT', 'edit.php');
define('FILENAME_COLLECTION_DEFAULT_THEME', 'default');
define('FILENAME_PAGE_TEMPLATE_DEFAULT_ICON', 'full.png');
define('FILENAME_PAGE_ICON', 'icon.png');
define('FILENAME_PACKAGE_CONTROLLER', 'controller.php');
define('FILENAME_PACKAGE_DB', 'db.xml');
define("FILENAME_LOCAL_DB", 'site_db.xml');
define('FILENAME_ATTRIBUTE_CONTROLLER', 'controller.php');
define('FILENAME_ATTRIBUTE_DB', 'db.xml');
define('FILENAME_AUTHENTICATION_CONTROLLER', 'controller.php');
define('FILENAME_AUTHENTICATION_DB', 'db.xml');
define('FILENAME_DB', 'db.xml');
define('FILENAME_COLLECTION_CONTROLLER', 'controller.php');
define('FILENAME_MENU_ITEM_CONTROLLER', 'controller.php');
define('FILENAME_CONTROLLER', 'controller.php');
define('FILENAME_THEMES_DESCRIPTION', 'description.txt');
define('FILENAME_THEMES_DEFAULT', 'default.php');
define('FILENAME_THEMES_VIEW', 'view.php');
define('FILENAME_THEMES_CLASS', 'page_theme.php');
define('FILENAME_THEMES_THUMBNAIL', 'thumbnail.png');
define('FILENAME_THEMES_ERROR', 'error');
define('FILENAME_GATHERING_DATA_SOURCE_OPTIONS', 'options.php');
define('FILENAME_GATHERING_ITEM_TEMPLATE_ICON', 'icon.png');
define('FILENAME_CONVERSATION_EDITOR_OPTIONS', 'options.php');
define('FILENAME_CONVERSATION_EDITOR_FORM_MESSAGE', 'message.php');
define('FILENAME_CONVERSATION_EDITOR_FORM_REPLY', 'reply.php');
define('FILENAME_STYLE_CUSTOMIZER_STYLES', 'styles.xml');
define('FILENAME_STYLE_CUSTOMIZER_DEFAULT_PRESET_NAME', 'defaults.less');


/**
 * ----------------------------------------------------------------------------
 * Directory constants
 * ----------------------------------------------------------------------------
 */
define('DIR_BASE_CORE', realpath(dirname(__FILE__) . '/..'));
define('DIR_PACKAGES', DIR_BASE . '/packages');
define('DIR_FILES_BLOCK_TYPES', DIR_APPLICATION . '/' . DIRNAME_BLOCKS);
define('DIR_FILES_BLOCK_TYPES_CORE', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
define('DIR_FILES_TOOLS', DIR_APPLICATION . '/tools');
define('DIR_FILES_TOOLS_REQUIRED', DIR_BASE_CORE . '/tools');
define('DIR_PACKAGES_CORE', DIR_BASE_CORE . '/packages');
define('DIR_STARTING_POINT_PACKAGES', DIR_APPLICATION . '/config/install/packages');
define('DIR_STARTING_POINT_PACKAGES_CORE', DIR_BASE_CORE . '/config/install/packages');
define('DIR_CORE_UPDATES', DIR_BASE . '/' . DIRNAME_UPDATES);
define('DIR_FILES_PAGE_TEMPLATE_ICONS', DIR_BASE_CORE . '/images/icons/page_templates');
define('DIR_FILES_CONTENT', DIR_APPLICATION . '/single_pages');
define('DIR_FILES_CONTENT_REQUIRED', DIR_BASE_CORE . '/single_pages');
define('DIR_FILES_CONTROLLERS', DIR_APPLICATION . '/controllers');
define('DIR_FILES_CONTROLLERS_REQUIRED', DIR_BASE_CORE . '/controllers');
define('DIR_FILES_ELEMENTS', DIR_APPLICATION . '/elements');
define('DIR_FILES_ELEMENTS_CORE', DIR_BASE_CORE . '/elements');
define('DIR_FILES_JOBS', DIR_APPLICATION . '/jobs');
define('DIR_FILES_JOBS_CORE', DIR_BASE_CORE . '/jobs');
define('DIR_FILES_THEMES', DIR_APPLICATION . '/themes');
define('DIR_FILES_THEMES_CORE', DIR_BASE_CORE . '/themes');
define('DIR_FILES_THEMES_CORE_ADMIN', DIR_BASE_CORE . '/themes/core');
define('DIR_LANGUAGES', DIR_APPLICATION . '/' . DIRNAME_LANGUAGES);
define('DIR_LANGUAGES_CORE', DIR_BASE_CORE . '/' . DIRNAME_LANGUAGES);
define('DIR_FILES_EMAIL_TEMPLATES', DIR_APPLICATION . '/mail');
define('DIR_FILES_EMAIL_TEMPLATES_CORE', DIR_BASE_CORE . '/mail');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL', DIR_FILES_BLOCK_TYPES . '/external_form/form/');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS', DIR_FILES_BLOCK_TYPES . '/external_form/form/controller');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/form');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/form/controller');
define('DIR_FILES_UPLOADED_STANDARD', DIR_APPLICATION . '/files');
define('DIR_FILES_BACKUPS', DIR_FILES_UPLOADED_STANDARD . '/backups');
define('DIR_AL_ICONS', DIR_BASE_CORE . '/images/icons/filetypes');
define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);



/**
 * ----------------------------------------------------------------------------
 * Internal proxy block types
 * ----------------------------------------------------------------------------
 */
define('BLOCK_HANDLE_SCRAPBOOK_PROXY', 'core_scrapbook_display');
define('BLOCK_HANDLE_LAYOUT_PROXY', 'core_area_layout');
define('BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY', 'core_page_type_composer_control_output');
define('BLOCK_HANDLE_STACK_PROXY', 'core_stack_display');
define('BLOCK_HANDLE_GATHERING', 'core_gathering');
define('BLOCK_HANDLE_GATHERING_ITEM_PROXY', 'core_gathering_item');
define('BLOCK_HANDLE_GATHERING_PROXY', 'core_gathering_display');
define('BLOCK_HANDLE_CONVERSATION', 'core_conversation');
define('BLOCK_HANDLE_CONVERSATION_MESSAGE', 'core_conversation_message');



/**
 * ----------------------------------------------------------------------------
 * Stack Defaults
 * ----------------------------------------------------------------------------
 */
define('STACKS_LISTING_PAGE_PATH', '/dashboard/blocks/stacks');
define('STACKS_PAGE_PATH', '/!stacks');
define('STACKS_AREA_NAME', 'Main');
define('STACKS_PAGE_TYPE', 'core_stack');



/**
 * ----------------------------------------------------------------------------
 * Configuration values that cannot be overridden
 * ----------------------------------------------------------------------------
 */
/* -- Appearance -- */
define('VIEW_CORE_THEME', 'concrete');

/* -- Users -- */
define('USER_SUPER', 'admin');
define('USER_SUPER_ID', 1);
define('GUEST_GROUP_ID', '1');
define('REGISTERED_GROUP_ID', '2');
define('ADMIN_GROUP_ID', '3');
define('USER_FOREVER_COOKIE_LIFETIME', 1209600); // 14 days
define('USER_CHANGE_PASSWORD_URL_LIFETIME',  7200);
define('ONLINE_NOW_TIMEOUT', 300);
define('UVTYPE_REGISTER', 0);
define('UVTYPE_CHANGE_PASSWORD', 1);
define('UVTYPE_LOGIN_FOREVER', 2);
define('NEWSFLOW_VIEWED_THRESHOLD', 86400); // once a day

/* -- Pages -- */
define('CHECKOUT_TIMEOUT', 300); // # in seconds.
define('VERSION_INITIAL_COMMENT', 'Initial Version');
define("HOME_CID", 1);
define("HOME_NAME", "Home");
define('HOME_UID', USER_SUPER_ID);
define('HOME_HANDLE', "home");

/* -- Errors -- */
define('COLLECTION_NOT_FOUND', 10);
define('COLLECTION_INIT', 11);
define('COLLECTION_FORBIDDEN', 12);
define('VERSION_NOT_RECENT', 50);
define('USER_INVALID', 20);
define('USER_INACTIVE', 21);
define('USER_NON_VALIDATED', 22);
define('USER_SESSION_EXPIRED', 23);
define('COLLECTION_MASTER_UNAUTH', 30);
define('COLLECTION_PRIVATE', 40);
define('BLOCK_NOT_AVAILABLE', 50);

/* -- Debugging and Logging -- */
define('DEBUG_DISPLAY_PRODUCTION', 0);
define('DEBUG_DISPLAY_ERRORS', 1);
define('DEBUG_DISPLAY_ERRORS_SQL', 2); // not used
define('LOG_TYPE_EMAILS', 'sent_emails');
define('LOG_TYPE_EXCEPTIONS', 'exceptions');



/**
 * ----------------------------------------------------------------------------
 * concrete5 depends on some more forgiving error handling.
 * ----------------------------------------------------------------------------
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);


/**
 * ----------------------------------------------------------------------------
 * Define computed permissions for files and directories
 * ----------------------------------------------------------------------------
 *
 */
// Set directory permissions to that of DIR_FILES_UPLOADED_STANDARD. Or if that can't be found, 0775.
$DIRECTORY_PERMISSIONS_MODE = (($p = @fileperms(DIR_FILES_UPLOADED_STANDARD) & 0777) > 0) ? $p : 0775;
$FILE_PERMISSIONS_MODE = '';
foreach(str_split(decoct($DIRECTORY_PERMISSIONS_MODE), 1) as $p) {
    if (intval($p) % 2 == 0) {
        $FILE_PERMISSIONS_MODE .= $p;
        continue;
    }
    $FILE_PERMISSIONS_MODE .= intval($p) - 1;
}
$FILE_PERMISSIONS_MODE = octdec($FILE_PERMISSIONS_MODE);
define('DIRECTORY_PERMISSIONS_MODE_COMPUTED', $DIRECTORY_PERMISSIONS_MODE);
define('FILE_PERMISSIONS_MODE_COMPUTED', $FILE_PERMISSIONS_MODE);


/**
 * ----------------------------------------------------------------------------
 * We need our include path to be set here for libraries like Zend Framework
 * ----------------------------------------------------------------------------
 */
ini_set('include_path', DIR_BASE_CORE . DIRECTORY_SEPARATOR . DIRNAME_VENDOR . PATH_SEPARATOR . get_include_path());


/**
 * ----------------------------------------------------------------------------
 * Load some helper functions
 * ----------------------------------------------------------------------------
 */
require dirname(__FILE__) . '/helpers.php';
