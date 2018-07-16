<?php

/*
 * ----------------------------------------------------------------------------
 * Ensure that all subsequent procedural pages are running inside concrete5.
 * ----------------------------------------------------------------------------
 */
defined('C5_EXECUTE') or define('C5_EXECUTE', md5(uniqid()));

/*
 * ----------------------------------------------------------------------------
 * Ensure that we have a currently defined time zone.
 * This needs to be done very early in order to avoid Whoops quitting with
 * "It is not safe to rely on the system's timezone settings."
 * ----------------------------------------------------------------------------
 */
@date_default_timezone_set(@date_default_timezone_get() ?: 'UTC');

/*
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
defined('DIR_BASE') or define('DIR_BASE', str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['SCRIPT_FILENAME'])));
defined('DIR_APPLICATION') or define('DIR_APPLICATION', DIR_BASE . '/' . DIRNAME_APPLICATION);
defined('DIR_CONFIG_SITE') or define('DIR_CONFIG_SITE', DIR_APPLICATION . '/config');

/*
 * ----------------------------------------------------------------------------
 * Now that we've had the opportunity to load our config file, we know if we
 * have a DIRNAME_CORE_UPDATED constant, which lives in that file, and which
 * points to another core. If we have this constant, we exit this file
 * immeditely and proceed into the updated core.
 * ----------------------------------------------------------------------------
 */

if (!defined('APP_UPDATED_PASSTHRU')) {
    $update_file = DIR_CONFIG_SITE . '/update.php';
    if (file_exists($update_file)) {
        $updates = (array) include $update_file;
        if (isset($updates['core'])) {
            define('APP_UPDATED_PASSTHRU', true);
            define('DIRNAME_APP_UPDATED', $updates['core']);
            if (is_dir(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED)) {
                require DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php';
            } elseif (file_exists(DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php')) {
                require DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php';
            } else {
                die(sprintf('Invalid "%s" defined. Please remove it from %s.', 'update.core', $update_file));
            }
            exit;
        }
    }
    unset($update_file);
    define('APP_UPDATED_PASSTHRU', false);
}

if (!defined('DIRNAME_APP_UPDATED') && isset($updates['core'])) {
    define('DIRNAME_APP_UPDATED', $updates['core']);
}

/*
 * ----------------------------------------------------------------------------
 * ## If we're still here, we're proceeding through this concrete directory,
 * and it's time to load the rest of our hard-coded configuration options –
 * the one we don't need a database to tell us about.
 *
 * Namespacing and Autoloading
 * ----------------------------------------------------------------------------
 */
const NAMESPACE_SEGMENT_VENDOR = 'Concrete';

/*
 * ----------------------------------------------------------------------------
 * Directory names
 * ----------------------------------------------------------------------------
 */
const DIRNAME_BLOCKS = 'blocks';
const DIRNAME_PAGES = 'single_pages';
const DIRNAME_VIEWS = 'views';
const DIRNAME_PACKAGES = 'packages';
const DIRNAME_MODELS = 'models';
const DIRNAME_ATTRIBUTE = 'attribute';
const DIRNAME_ATTRIBUTES = 'attributes';
const DIRNAME_MENU_ITEMS = 'menu_items';
const DIRNAME_AUTHENTICATION = 'authentication';
const DIRNAME_LIBRARIES = 'libraries';
const DIRNAME_RESPONSE = 'response';
const DIRNAME_PERMISSIONS = 'permission';
const DIRNAME_WORKFLOW = 'workflow';
const DIRNAME_WORKFLOW_ASSIGNMENTS = 'assignments';
const DIRNAME_REQUESTS = 'requests';
const DIRNAME_KEYS = 'keys';
const DIRNAME_PAGE_TYPES = 'page_types';
const DIRNAME_PAGE_TEMPLATES = 'page_templates';
const DIRNAME_PAGE_THEME = 'page_theme';
const DIRNAME_PAGE_THEME_CUSTOM = 'custom';
const DIRNAME_ELEMENTS = 'elements';
const DIRNAME_LANGUAGES = 'languages';
const DIRNAME_JOBS = 'jobs';
const DIRNAME_DASHBOARD = 'dashboard';
const DIRNAME_ELEMENTS_HEADER_MENU = 'header_menu';
const DIRNAME_DASHBOARD_MODULES = 'modules';
const DIRNAME_MAIL_TEMPLATES = 'mail';
const DIRNAME_THEMES = 'themes';
const DIRNAME_THEMES_CORE = 'core';
const DIRNAME_CONFIG = 'config';
const DIRNAME_TOOLS = 'tools';
const DIRNAME_BLOCK_TOOLS = 'tools';
const DIRNAME_BLOCK_TEMPLATES = 'templates';
const DIRNAME_BLOCK_TEMPLATES_COMPOSER = 'composer';
const DIRNAME_CSS = 'css';
const DIRNAME_CLASSES = 'src';
const DIRNAME_ENTITIES = 'Entity';
const DIRNAME_PREVIEW = 'preview';
const DIRNAME_GROUP = 'group';
const DIRNAME_GROUP_AUTOMATION = 'automation';
const DIRNAME_JAVASCRIPT = 'js';
const DIRNAME_IMAGES = 'images';
const DIRNAME_IMAGES_LANGUAGES = 'countries';
const DIRNAME_HELPERS = 'helpers';
const DIRNAME_USER_POINTS = 'user_point';
const DIRNAME_ACTIONS = 'actions';
const DIRNAME_SYSTEM_TYPES = 'types';
const DIRNAME_SYSTEM_CAPTCHA = 'captcha';
const DIRNAME_SYSTEM_ANTISPAM = 'antispam';
const DIRNAME_SYSTEM = 'system';
const DIRNAME_PANELS = 'panels';
const DIRNAME_CONTROLLERS = 'controllers';
const DIRNAME_PAGE_CONTROLLERS = 'single_page';
const DIRNAME_GATHERING = 'gathering';
const DIRNAME_GATHERING_DATA_SOURCES = 'data_sources';
const DIRNAME_GATHERING_ITEM_TEMPLATES = 'templates';
const DIRNAME_COMPOSER = 'composer';
const DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES = 'target_types';
const DIRNAME_COMPOSER_ELEMENTS_CONTROLS = 'controls';
const DIRNAME_ELEMENTS_PAGE_TYPES_PUBLISH_TARGET_TYPES_FORM = 'form';
const DIRNAME_CONVERSATIONS = 'conversation';
const DIRNAME_CONVERSATION_EDITOR = 'editor';
const DIRNAME_VENDOR = 'vendor';
const DIRNAME_LANGUAGES_SITE_INTERFACE = 'site';
const DIRNAME_STYLE_CUSTOMIZER = 'style_customizer';
const DIRNAME_STYLE_CUSTOMIZER_TYPES = 'types';
const DIRNAME_STYLE_CUSTOMIZER_PRESETS = 'presets';
const DIRNAME_FILE_STORAGE_LOCATION_TYPES = 'storage_location_types';
const DIRNAME_EXPRESS = 'express';
const DIRNAME_EXPRESS_VIEW_CONTROLS = 'view';
const DIRNAME_EXPRESS_CONTROL_OPTIONS = 'control';
const DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES = 'form';
const DIRNAME_EXPRESS_FORM_CONTROLS = 'form';
const DIRNAME_EXPRESS_FORM_CONTROLS_ASSOCIATION = 'association';
const DIRNAME_METADATA_XML = 'xml';
const DIRNAME_METADATA_YAML = 'yaml';
const DIRNAME_GEOLOCATION = 'geolocation';
const REL_DIR_FILES_INCOMING = '/incoming';
const REL_DIR_FILES_THUMBNAILS = '/thumbnails';
define('REL_DIR_METADATA_XML', DIRNAME_CONFIG . '/' . DIRNAME_METADATA_XML);
define('REL_DIR_METADATA_YAML', DIRNAME_CONFIG . '/' . DIRNAME_METADATA_YAML);

/*
 * ----------------------------------------------------------------------------
 * Config location/path
 * ----------------------------------------------------------------------------
 */
const CONFIG_ORM_METADATA_BASE = 'database.metadatadriver';
define('CONFIG_ORM_METADATA_PACKAGES_BASE', CONFIG_ORM_METADATA_BASE . '.packages');
define('CONFIG_ORM_METADATA_ANNOTATION_LEGACY', CONFIG_ORM_METADATA_PACKAGES_BASE . '.annotation.legacy');
define('CONFIG_ORM_METADATA_ANNOTATION_DEFAULT', CONFIG_ORM_METADATA_PACKAGES_BASE . '.annotation.default');
define('CONFIG_ORM_METADATA_XML', CONFIG_ORM_METADATA_PACKAGES_BASE . '.xml');
define('CONFIG_ORM_METADATA_YAML', CONFIG_ORM_METADATA_PACKAGES_BASE . '.yaml');
define('CONFIG_ORM_METADATA_APPLICATION', CONFIG_ORM_METADATA_BASE . '.application');

/*
 * ----------------------------------------------------------------------------
 * File names
 * ----------------------------------------------------------------------------
 */
const FILENAME_BLOCK_VIEW = 'view.php';
const FILENAME_BLOCK_COMPOSER = 'composer.php';
const FILENAME_BLOCK_VIEW_SCRAPBOOK = 'scrapbook.php';
const FILENAME_BLOCK_ADD = 'add.php';
const FILENAME_BLOCK_EDIT = 'edit.php';
const FILENAME_BLOCK_ICON = 'icon.png';
const FILENAME_BLOCK_CONTROLLER = 'controller.php';
const FILENAME_BLOCK_DB = 'db.xml';
const FILENAME_FORM = 'form.php';
const FILENAME_COLLECTION_VIEW = 'view.php';
const FILENAME_COLLECTION_ACCESS = 'access.xml';
const FILENAME_COLLECTION_EDIT = 'edit.php';
const FILENAME_COLLECTION_DEFAULT_THEME = 'default';
const FILENAME_PAGE_TEMPLATE_DEFAULT_ICON = 'full.png';
const FILENAME_PAGE_ICON = 'icon.png';
const FILENAME_PACKAGE_CONTROLLER = 'controller.php';
const FILENAME_PACKAGE_DB = 'db.xml';
const FILENAME_LOCAL_DB = 'site_db.xml';
const FILENAME_ATTRIBUTE_CONTROLLER = 'controller.php';
const FILENAME_ATTRIBUTE_DB = 'db.xml';
const FILENAME_AUTHENTICATION_CONTROLLER = 'controller.php';
const FILENAME_AUTHENTICATION_DB = 'db.xml';
const FILENAME_DB = 'db.xml';
const FILENAME_COLLECTION_CONTROLLER = 'controller.php';
const FILENAME_MENU_ITEM_CONTROLLER = 'controller.php';
const FILENAME_CONTROLLER = 'controller.php';
const FILENAME_THEMES_DESCRIPTION = 'description.txt';
const FILENAME_THEMES_DEFAULT = 'default.php';
const FILENAME_THEMES_VIEW = 'view.php';
const FILENAME_THEMES_CLASS = 'page_theme.php';
const FILENAME_THEMES_THUMBNAIL = 'thumbnail.png';
const FILENAME_THEMES_ERROR = 'error';
const FILENAME_EXPRESS_CONTROL_OPTIONS = 'options.php';
const FILENAME_GATHERING_DATA_SOURCE_OPTIONS = 'options.php';
const FILENAME_GATHERING_ITEM_TEMPLATE_ICON = 'icon.png';
const FILENAME_CONVERSATION_EDITOR_OPTIONS = 'options.php';
const FILENAME_STYLE_CUSTOMIZER_STYLES = 'styles.xml';
const FILENAME_STYLE_CUSTOMIZER_DEFAULT_PRESET_NAME = 'defaults.less';

/*
 * ----------------------------------------------------------------------------
 * Directory constants
 * ----------------------------------------------------------------------------
 */
define('DIR_BASE_CORE', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__) . '/..')));
define('DIR_PACKAGES', DIR_BASE . '/packages');
define('DIR_FILES_BLOCK_TYPES', DIR_APPLICATION . '/' . DIRNAME_BLOCKS);
define('DIR_FILES_BLOCK_TYPES_CORE', DIR_BASE_CORE . '/' . DIRNAME_BLOCKS);
define('DIR_FILES_TOOLS', DIR_APPLICATION . '/tools');
define('DIR_FILES_TOOLS_REQUIRED', DIR_BASE_CORE . '/tools');
define('DIR_PACKAGES_CORE', DIR_BASE_CORE . '/packages');
defined('DIR_STARTING_POINT_PACKAGES') or define('DIR_STARTING_POINT_PACKAGES', DIR_CONFIG_SITE . '/install/packages');
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
define('DIR_AL_ICONS', DIR_BASE_CORE . '/images/icons/filetypes');
define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);

/*
 * ----------------------------------------------------------------------------
 * Internal proxy block types
 * ----------------------------------------------------------------------------
 */
const BLOCK_HANDLE_SCRAPBOOK_PROXY = 'core_scrapbook_display';
const BLOCK_HANDLE_LAYOUT_PROXY = 'core_area_layout';
const BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY = 'core_page_type_composer_control_output';
const BLOCK_HANDLE_STACK_PROXY = 'core_stack_display';
const BLOCK_HANDLE_GATHERING = 'core_gathering';
const BLOCK_HANDLE_GATHERING_ITEM_PROXY = 'core_gathering_item';
const BLOCK_HANDLE_GATHERING_PROXY = 'core_gathering_display';
const BLOCK_HANDLE_CONVERSATION = 'core_conversation';
const BLOCK_HANDLE_CONVERSATION_MESSAGE = 'core_conversation_message';

/*
 * ----------------------------------------------------------------------------
 * Stack Defaults
 * ----------------------------------------------------------------------------
 */
const STACKS_LISTING_PAGE_PATH = '/dashboard/blocks/stacks';
const STACKS_PAGE_PATH = '/!stacks';
const STACKS_AREA_NAME = 'Main';
const STACKS_PAGE_TYPE = 'core_stack';
const STACK_CATEGORY_PAGE_TYPE = 'core_stack_category';

/*
 * ----------------------------------------------------------------------------
 * Configuration values that cannot be overridden
 * ----------------------------------------------------------------------------
 */
/* -- Appearance -- */
const VIEW_CORE_THEME = 'concrete';
const VIEW_CORE_THEME_TEMPLATE_BACKGROUND_IMAGE = 'background_image.php';

/* -- Users -- */
const USER_SUPER = 'admin';
const USER_SUPER_ID = 1;
const GUEST_GROUP_ID = '1';
const REGISTERED_GROUP_ID = '2';
const ADMIN_GROUP_ID = '3';
const USER_FOREVER_COOKIE_LIFETIME = 1209600; // 14 days
const USER_CHANGE_PASSWORD_URL_LIFETIME = 7200;
const ONLINE_NOW_TIMEOUT = 300;
const UVTYPE_REGISTER = 0;
const UVTYPE_CHANGE_PASSWORD = 1;
const UVTYPE_LOGIN_FOREVER = 2;

/* -- Pages -- */
const CHECKOUT_TIMEOUT = 300; // # in seconds.
const VERSION_INITIAL_COMMENT = 'Initial Version';
/**
 * @deprecated Use Page::getHomePageID()
 */
const HOME_CID = 1;
const HOME_NAME = 'Home';
const HOME_UID = USER_SUPER_ID;
const HOME_HANDLE = 'home';

/* -- Errors -- */
const COLLECTION_NOT_FOUND = 10;
const COLLECTION_INIT = 11;
const COLLECTION_FORBIDDEN = 12;
const VERSION_NOT_RECENT = 50;
const USER_INVALID = 20;
const USER_INACTIVE = 21;
const USER_NON_VALIDATED = 22;
const USER_SESSION_EXPIRED = 23;
const COLLECTION_MASTER_UNAUTH = 30;
const COLLECTION_PRIVATE = 40;
const BLOCK_NOT_AVAILABLE = 50;

/* -- Debugging and Logging -- */
defined('DEFAULT_ERROR_REPORTING') or define('DEFAULT_ERROR_REPORTING', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
const DEBUG_DISPLAY_PRODUCTION = 0;
const DEBUG_DISPLAY_ERRORS = 1;
const DEBUG_DISPLAY_ERRORS_SQL = 2; // not used
const LOG_TYPE_EMAILS = 'sent_emails';
const LOG_TYPE_EXCEPTIONS = 'exceptions';

/*
 * ----------------------------------------------------------------------------
 * concrete5 depends on some more forgiving error handling.
 * ----------------------------------------------------------------------------
 */
error_reporting(DEFAULT_ERROR_REPORTING);

/*
 * ----------------------------------------------------------------------------
 * Define computed permissions for files and directories
 * ----------------------------------------------------------------------------
 *
 */
// Set directory permissions to that of DIR_FILES_UPLOADED_STANDARD. Or if that can't be found, 0775.
$DIRECTORY_PERMISSIONS_MODE = (($p = @fileperms(DIR_FILES_UPLOADED_STANDARD) & 0777) > 0) ? $p : 0775;
$FILE_PERMISSIONS_MODE = '';
foreach (str_split(decoct($DIRECTORY_PERMISSIONS_MODE), 1) as $p) {
    if (intval($p) % 2 == 0) {
        $FILE_PERMISSIONS_MODE .= $p;
        continue;
    }
    $FILE_PERMISSIONS_MODE .= intval($p) - 1;
}
unset($p);
$FILE_PERMISSIONS_MODE = octdec($FILE_PERMISSIONS_MODE);
define('DIRECTORY_PERMISSIONS_MODE_COMPUTED', $DIRECTORY_PERMISSIONS_MODE);
unset($DIRECTORY_PERMISSIONS_MODE);
define('FILE_PERMISSIONS_MODE_COMPUTED', $FILE_PERMISSIONS_MODE);
unset($FILE_PERMISSIONS_MODE);
/*
 * ----------------------------------------------------------------------------
 * We need our include path to be set here for libraries like Zend Framework
 * ----------------------------------------------------------------------------
 */
ini_set('include_path', DIR_BASE_CORE . '/' . DIRNAME_VENDOR . PATH_SEPARATOR . get_include_path());

/**
 * ----------------------------------------------------------------------------
 * Load some helper functions
 * ----------------------------------------------------------------------------.
 */
require dirname(__FILE__) . '/helpers.php';
