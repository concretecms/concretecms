<?php

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
defined('CONFIG_FILE') or define('CONFIG_FILE', DIR_CONFIG_SITE . '/site.php');



/**
 * ----------------------------------------------------------------------------
 * Developer preview constants.
 * ----------------------------------------------------------------------------
 */
define('ENABLE_MARKETPLACE_SUPPORT', false);
define('ENABLE_APP_NEWS_OVERLAY', false);
define('ENABLE_APP_NEWS', false);



/**
 * ----------------------------------------------------------------------------
 * Now we test to see if we have a valid site configuration file. If not, we're
 * going to need to render install.
 * ----------------------------------------------------------------------------
 */
if (file_exists(CONFIG_FILE)) define('CONFIG_FILE_EXISTS', true) and include(CONFIG_FILE);



/**
 * ----------------------------------------------------------------------------
 * Now that we've had the opportunity to load our config file, we know if we
 * have a DIRNAME_CORE_UPDATED constant, which lives in that file, and which
 * points to another core. If we have this constant, we exit this file
 * immeditely and proceed into the updated core.
 * ----------------------------------------------------------------------------
 */
if (defined('DIRNAME_CORE_UPDATED') && (!defined('APP_UPDATED_PASSTHRU'))) {
    define('APP_UPDATED_PASSTHRU', true);
    if (is_dir(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_CORE_UPDATED)) {
        require(DIR_BASE . '/' . DIRNAME_UPDATES . '/' . DIRNAME_CORE_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php');
    } else if(file_exists(DIRNAME_UPDATES . '/' . DIRNAME_CORE_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php')){
        require(DIRNAME_UPDATES . '/' . DIRNAME_CORE_UPDATED . '/' . DIRNAME_CORE . '/' . 'dispatcher.php');
    } else {
        die(sprintf('Invalid "%s" defined. Please remove it from %s.','DIRNAME_CORE_UPDATED', CONFIG_FILE));
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
defined('NAMESPACE_SEGMENT_VENDOR') or define('NAMESPACE_SEGMENT_VENDOR', 'Concrete');
defined('NAMESPACE_SEGMENT_APPLICATION') or define('NAMESPACE_SEGMENT_APPLICATION', 'Application');



/**
 * ----------------------------------------------------------------------------
 * Base URL, Relative Directory and URL rewriting
 * ----------------------------------------------------------------------------
 */
defined('REDIRECT_TO_BASE_URL') or define('REDIRECT_TO_BASE_URL', false);
defined('URL_REWRITING_ALL') or define('URL_REWRITING_ALL', false);
if (!defined('BASE_URL')) {
    if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
        define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST']);
    } else if (isset($_SERVER['HTTP_HOST'])) {
        define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
    } else {
        define('BASE_URL', false);
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
define('DIRNAME_AUTHENTICATION', 'authentication');
define('DIRNAME_LIBRARIES', 'libraries');
define('DIRNAME_RESPONSE', 'response');
define('DIRNAME_PERMISSIONS', 'permission');
define('DIRNAME_WORKFLOW', 'workflow');
define('DIRNAME_WORKFLOW_ASSIGNMENTS', 'assignments');
define('DIRNAME_REQUESTS', 'requests');
define('DIRNAME_KEYS', 'keys');
define('DIRNAME_PAGE_TYPES', 'page_types');
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
define('DIRNAME_CLASSES', 'core');
define('DIRNAME_PREVIEW', 'preview');
define('DIRNAME_GROUP', 'group');
define('DIRNAME_GROUP_AUTOMATION', 'automation');
define('DIRNAME_JAVASCRIPT', 'js');
define('DIRNAME_IMAGES', 'images');
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
define('REL_DIR_FILES_THUMBNAILS_LEVEL2', '/thumbnails/level2');
define('REL_DIR_FILES_THUMBNAILS_LEVEL3', '/thumbnails/level3');




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
define('FILENAME_PAGE_TEMPLATE_DEFAULT_ICON', 'main.png');
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
defined('DIR_BASE_CORE') or define('DIR_BASE_CORE', realpath(dirname(__FILE__) . '/..'));
defined('DIR_PACKAGES') or define('DIR_PACKAGES', DIR_BASE . '/packages');

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
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL', DIR_FILES_BLOCK_TYPES . '/external_form/forms/');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS', DIR_FILES_BLOCK_TYPES . '/external_form/forms/controllers');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/forms');
define('DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_PROCESS_CORE', DIR_FILES_BLOCK_TYPES_CORE . '/external_form/forms/controllers');
define('DIR_FILES_UPLOADED_STANDARD', DIR_APPLICATION . '/files');
define('DIR_FILES_BACKUPS', DIR_FILES_UPLOADED_STANDARD . '/backups');
define('DIR_AL_ICONS', DIR_BASE_CORE . '/images/icons/filetypes');
define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);



/**
 * ----------------------------------------------------------------------------
 * Assets (Images, JS, etc....) URLs
 * ----------------------------------------------------------------------------
 */
if (defined('DIRNAME_CORE_UPDATED')) {
    $ap = DIR_REL . '/' . DIRNAME_UPDATES . '/' . DIRNAME_CORE_UPDATED . '/' . DIRNAME_CORE;
} else {
    $ap = DIR_REL . '/' . DIRNAME_CORE;
}
define('ASSETS_URL', $ap);
define('ASSETS_URL_CSS', $ap . '/css');
define('ASSETS_URL_JAVASCRIPT', $ap . '/js');
define('ASSETS_URL_IMAGES', $ap . '/images');



/**
 * ----------------------------------------------------------------------------
 * Cache defaults
 * ----------------------------------------------------------------------------
 */
defined('DIR_FILES_CACHE') or define('DIR_FILES_CACHE', DIR_FILES_UPLOADED_STANDARD . '/cache');
defined('FILENAME_ENVIRONMENT_CACHE') or define('FILENAME_ENVIRONMENT_CACHE', 'environment.cache');
defined('DIR_FILES_PAGE_CACHE') or define('DIR_FILES_PAGE_CACHE', DIR_FILES_CACHE . '/pages');
defined('PAGE_CACHE_LIBRARY') or define('PAGE_CACHE_LIBRARY', 'file');
defined('CACHE_ID') or define('CACHE_ID', md5(str_replace(array('https://', 'http://'), '', BASE_URL) . DIR_REL));
defined('CACHE_LIFETIME') or define('CACHE_LIFETIME', 21600); // 6 hours



/**
 * ----------------------------------------------------------------------------
 * Relative paths to certain directories and assets. Actually accesses file
 * system
 * ----------------------------------------------------------------------------
 */
define('REL_DIR_APPLICATION', DIR_REL . '/' . DIRNAME_APPLICATION);
define('REL_DIR_STARTING_POINT_PACKAGES', REL_DIR_APPLICATION . '/config/install/packages');
define('REL_DIR_STARTING_POINT_PACKAGES_CORE', ASSETS_URL . '/config/install/packages');
define('REL_DIR_PACKAGES', DIR_REL . '/packages');
define('REL_DIR_PACKAGES_CORE', ASSETS_URL . '/packages');
define('REL_DIR_FILES_PAGE_TEMPLATE_ICONS', ASSETS_URL_IMAGES . '/icons/page_templates');
define('REL_DIR_FILES_UPLOADED_STANDARD', REL_DIR_APPLICATION . '/files');
define('REL_DIR_FILES_TRASH_STANDARD', REL_DIR_FILES_UPLOADED_STANDARD . '/trash');
define('REL_DIR_FILES_CACHE', REL_DIR_FILES_UPLOADED_STANDARD . '/cache');
define('REL_DIR_AL_ICONS', ASSETS_URL_IMAGES . '/icons/filetypes');
define('REL_DIR_FILES_AVATARS', '/avatars');
define('REL_DIR_LANGUAGES_SITE_INTERFACE', REL_DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);



/**
 * ----------------------------------------------------------------------------
 * Relative paths to tools. Passes through concrete5.
 * ----------------------------------------------------------------------------
 */
if (URL_REWRITING_ALL == true) {
    define('REL_DIR_FILES_TOOLS', DIR_REL . '/tools');
    define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/tools/required'); // front-end
} else {
    define('REL_DIR_FILES_TOOLS', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools');
    define('REL_DIR_FILES_TOOLS_REQUIRED', DIR_REL . '/' . DISPATCHER_FILENAME . '/tools/required'); // front-end
}
define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
define('REL_DIR_FILES_TOOLS_PACKAGES', REL_DIR_FILES_TOOLS . '/packages');



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
define('STACKS_PAGE_PATH', '/!stacks');
define('STACKS_AREA_NAME', 'Main');
define('STACKS_PAGE_TYPE', 'core_stack');



/**
 * ----------------------------------------------------------------------------
 * Email Defaults
 * ----------------------------------------------------------------------------
 */
defined('ENABLE_EMAILS') or define('ENABLE_EMAILS', true);
defined('EMAIL_DEFAULT_FROM_ADDRESS') or define('EMAIL_DEFAULT_FROM_ADDRESS',
    'concrete5-noreply@' . str_replace(array('http://www.', 'https://www.', 'http://', 'https://'), '', BASE_URL)
);
defined('EMAIL_DEFAULT_FROM_NAME') or define('EMAIL_DEFAULT_FROM_NAME', '');



/**
 * ----------------------------------------------------------------------------
 * Queue and display performance limits
 * ----------------------------------------------------------------------------
 */
defined('SITEMAP_PAGES_LIMIT') or define('SITEMAP_PAGES_LIMIT', 100);
defined('DELETE_PAGES_LIMIT') or define('DELETE_PAGES_LIMIT', 10);
defined('COPY_PAGES_LIMIT') or define('COPY_PAGES_LIMIT', 10);
defined('PAGE_SEARCH_INDEX_BATCH_SIZE') or define('PAGE_SEARCH_INDEX_BATCH_SIZE', 200);
defined('JOB_QUEUE_BATCH_SIZE') or define('JOB_QUEUE_BATCH_SIZE', 10);



/**
 * ----------------------------------------------------------------------------
 * Character sets
 * ----------------------------------------------------------------------------
 */
defined('APP_CHARSET') or define('APP_CHARSET', 'UTF-8');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8');



/**
 * ----------------------------------------------------------------------------
 * Get our current version, along with setting defaults for when to check for
 * new versions.
 * ----------------------------------------------------------------------------
 */
require DIR_BASE_CORE . '/config/version.php';
define('APP_VERSION', $APP_VERSION);
define('APP_VERSION_LATEST_THRESHOLD', 172800); // Every 2 days we check for the latest version (this is seconds)
define('APP_VERSION_LATEST_WS', 'http://www.concrete5.org/tools/get_latest_version_number');
define('APP_VERSION_LATEST_DOWNLOAD', 'http://www.concrete5.org/download/');



/**
 * ----------------------------------------------------------------------------
 * Marketplace URL for in-site add-on browsing, installation
 * ----------------------------------------------------------------------------
 */
defined('CONCRETE5_ORG_URL') or define('CONCRETE5_ORG_URL', 'http://www.concrete5.org');
defined('CONCRETE5_ORG_URL_SECURE') or define('CONCRETE5_ORG_URL_SECURE', 'https://www.concrete5.org');
defined('NEWSFLOW_URL') or define('NEWSFLOW_URL', 'http://newsflow.concrete5.org');
defined('MENU_HELP_SERVICE_URL') or define('MENU_HELP_SERVICE_URL', CONCRETE5_ORG_URL . '/tools/get_remote_help_list/');
defined('MARKETPLACE_THEME_PREVIEW_URL') or define('MARKETPLACE_THEME_PREVIEW_URL', CONCRETE5_ORG_URL . '/tools/preview_theme/');

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
define('DASHBOARD_BACKGROUND_FEED', '//backgroundimages.concrete5.org/wallpaper');
define('DASHBOARD_BACKGROUND_FEED_SECURE', 'https://backgroundimages.concrete5.org/wallpaper');
define('DASHBOARD_BACKGROUND_INFO', 'http://backgroundimages.concrete5.org/get_image_data.php');



/**
 * ----------------------------------------------------------------------------
 * Changeable site behaviors and display preferences
 * ----------------------------------------------------------------------------
 */
/* -- Text, title formats -- */
defined('PAGE_TITLE_FORMAT') or define('PAGE_TITLE_FORMAT', '%1$s :: %2$s');
defined('PAGE_PATH_SEPARATOR') or define('PAGE_PATH_SEPARATOR', '-');
defined('PAGE_PATH_SEGMENT_MAX_LENGTH') or define('PAGE_PATH_SEGMENT_MAX_LENGTH', '128');
defined('PAGING_STRING') or define('PAGING_STRING', 'ccm_paging_p');
defined('TRASH_PAGE_PATH') or define('TRASH_PAGE_PATH', '/!trash');
defined('PAGE_DRAFTS_PAGE_PATH') or define('PAGE_DRAFTS_PAGE_PATH', '/!drafts');

/* -- Icon Sizes -- */
defined('PAGE_TEMPLATE_ICON_WIDTH') or define('PAGE_TEMPLATE_ICON_WIDTH', 120);
defined('PAGE_TEMPLATE_ICON_HEIGHT') or define('PAGE_TEMPLATE_ICON_HEIGHT', 90);
defined('THEMES_THUMBNAIL_WIDTH') or define('THEMES_THUMBNAIL_WIDTH', 120);
defined('THEMES_THUMBNAIL_HEIGHT') or define('THEMES_THUMBNAIL_HEIGHT', 90);
defined('AL_THUMBNAIL_WIDTH') or define('AL_THUMBNAIL_WIDTH', '60');
defined('AL_THUMBNAIL_HEIGHT') or define('AL_THUMBNAIL_HEIGHT', '60');
defined('AL_THUMBNAIL_WIDTH_LEVEL1') or define('AL_THUMBNAIL_WIDTH_LEVEL1', '60');
defined('AL_THUMBNAIL_HEIGHT_LEVEL1') or define('AL_THUMBNAIL_HEIGHT_LEVEL1', '60');
defined('AL_THUMBNAIL_WIDTH_LEVEL2') or define('AL_THUMBNAIL_WIDTH_LEVEL2', '250');
defined('AL_THUMBNAIL_HEIGHT_LEVEL2') or define('AL_THUMBNAIL_HEIGHT_LEVEL2', '250');

/* -- Sitemap.xml -- */
defined('SITEMAPXML_FILE') or define('SITEMAPXML_FILE', 'sitemap.xml');
defined('SITEMAPXML_DEFAULT_CHANGEFREQ') or define('SITEMAPXML_DEFAULT_CHANGEFREQ', 'weekly');
defined('SITEMAPXML_DEFAULT_PRIORITY') or define('SITEMAPXML_DEFAULT_PRIORITY', 0.5);
defined('SITEMAPXML_BASE_URL') or define('SITEMAPXML_BASE_URL', BASE_URL);

/* -- Miscellaneous Behavior -- */
defined('SITEMAP_APPROVE_IMMEDIATELY') or define('SITEMAP_APPROVE_IMMEDIATELY', true);
defined('ENABLE_TRANSLATE_LOCALE_EN_US') or define('ENABLE_TRANSLATE_LOCALE_EN_US', false);
defined('PAGE_SEARCH_INDEX_LIFETIME') or define('PAGE_SEARCH_INDEX_LIFETIME', 259200);
defined('ENABLE_TRASH_CAN') or define('ENABLE_TRASH_CAN', true);
defined('URL_USE_TRAILING_SLASH') or define('URL_USE_TRAILING_SLASH', false);
defined('ENABLE_AUTO_UPDATE_CORE') or define('ENABLE_AUTO_UPDATE_CORE', false);
defined('ENABLE_AUTO_UPDATE_PACKAGES') or define('ENABLE_AUTO_UPDATE_PACKAGES', false);
defined('APP_VERSION_DISPLAY_IN_HEADER') or define('APP_VERSION_DISPLAY_IN_HEADER', true);

/* -- File Sets -- */
defined('CONVERSATION_MESSAGE_ATTACHMENTS_PENDING_FILE_SET') or define('CONVERSATION_MESSAGE_ATTACHMENTS_PENDING_FILE_SET', 'Conversation Messages (Pending)');

/* -- Users -- */
defined('USER_USERNAME_MINIMUM') or define('USER_USERNAME_MINIMUM', 3);
defined('USER_USERNAME_MAXIMUM') or define('USER_USERNAME_MAXIMUM', 64);
defined('USER_PASSWORD_MINIMUM') or define('USER_PASSWORD_MINIMUM', 5);
defined('USER_PASSWORD_MAXIMUM') or define('USER_PASSWORD_MAXIMUM', 128);
defined('USER_USERNAME_ALLOW_SPACES') or define('USER_USERNAME_ALLOW_SPACES', false);
defined('GROUP_BADGE_DEFAULT_POINT_VALUE') or define('GROUP_BADGE_DEFAULT_POINT_VALUE', 50);
defined('NEWSFLOW_VIEWED_THRESHOLD') or define('NEWSFLOW_VIEWED_THRESHOLD', 86400); // once a day
defined('AVATAR_WIDTH') or define('AVATAR_WIDTH', '80');
defined('AVATAR_HEIGHT') or define('AVATAR_HEIGHT', '80');
defined('AVATAR_NONE') or define('AVATAR_NONE', ASSETS_URL_IMAGES . '/avatar_none.png');
defined('SESSION') or define('SESSION', 'CONCRETE5');
defined('USER_DELETED_CONVERSATION_ID') or define('USER_DELETED_CONVERSATION_ID', 0);
defined('PASSWORD_HASH_PORTABLE') or define('PASSWORD_HASH_PORTABLE', false);
defined('PASSWORD_HASH_COST_LOG2') or define('PASSWORD_HASH_COST_LOG2', 12);
defined('USER_PRIVATE_MESSAGE_MAX') or define('USER_PRIVATE_MESSAGE_MAX', 20);
defined('USER_PRIVATE_MESSAGE_MAX_TIME_SPAN') or define('USER_PRIVATE_MESSAGE_MAX_TIME_SPAN', '15'); // minutes;

/* -- Jobs -- */
defined('ENABLE_JOB_SCHEDULING') or define('ENABLE_JOB_SCHEDULING', true);



/**
 * ----------------------------------------------------------------------------
 * Configuration values that cannot be overridden
 * ----------------------------------------------------------------------------
 */
/* -- Appearance -- */
define('BLOCK_TYPE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_block_type_generic.png');
define('PACKAGE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_package_generic.png');
define('ASSETS_URL_THEMES_NO_THUMBNAIL', ASSETS_URL_IMAGES . '/spacer.gif');
define('VIEW_CORE_THEME', 'concrete');
define('AL_ICON_DEFAULT', ASSETS_URL_IMAGES . '/icons/filetypes/default.png');

/* -- Users -- */
define('USER_SUPER', 'admin');
define('USER_SUPER_ID', 1);
define('GUEST_GROUP_ID', '1');
define('REGISTERED_GROUP_ID', '2');
define('ADMIN_GROUP_ID', '3');
define('SESSION_MAX_LIFETIME', 7200); // 2 hours
define('USER_FOREVER_COOKIE_LIFETIME', 1209600); // 14 days
define('USER_CHANGE_PASSWORD_URL_LIFETIME',  7200);
define('ONLINE_NOW_TIMEOUT', 300);
define('UVTYPE_REGISTER', 0);
define('UVTYPE_CHANGE_PASSWORD', 1);
define('UVTYPE_LOGIN_FOREVER', 2);

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
 * We need our include path to be set here for libraries like Zend Framework
 * ----------------------------------------------------------------------------
 */
ini_set('include_path', DIR_BASE_CORE . DIRECTORY_SEPARATOR . DIRNAME_VENDOR . PATH_SEPARATOR . get_include_path());


/**
 * ----------------------------------------------------------------------------
 * Load some helper functions
 * ----------------------------------------------------------------------------
 */
require __DIR__ . '/helpers.php';



/**
 * ----------------------------------------------------------------------------
 * Set the timezone
 * ----------------------------------------------------------------------------
 */
if (defined('APP_TIMEZONE')) {
    define('APP_TIMEZONE_SERVER', @date_default_timezone_get());
    date_default_timezone_set(APP_TIMEZONE);
} else {
    date_default_timezone_set(@date_default_timezone_get());
}
