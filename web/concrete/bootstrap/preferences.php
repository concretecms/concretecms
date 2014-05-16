<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Logging preferences.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('SITE_DEBUG_LEVEL', DEBUG_DISPLAY_ERRORS);



/**
 * ----------------------------------------------------------------------------
 * File preferences.
 * ----------------------------------------------------------------------------
 */

/* -- Allowed extensions -- */
define('UPLOAD_FILE_EXTENSIONS_CONFIGURABLE', !defined('UPLOAD_FILE_EXTENSIONS_ALLOWED'));
Config::getOrDefine('UPLOAD_FILE_EXTENSIONS_ALLOWED','*.flv;*.jpg;*.gif;*.jpeg;*.ico;*.docx;*.xla;*.png;*.psd;*.swf;*.doc;*.txt;*.xls;*.xlsx;*.csv;*.pdf;*.tiff;*.rtf;*.m4a;*.mov;*.wmv;*.mpeg;*.mpg;*.wav;*.3gp;*.avi;*.m4v;*.mp4;*.mp3;*.qt;*.ppt;*.pptx;*.kml;*.xml;*.svg;*.webm;*.ogg;*.ogv');

/* -- Default directory -- */
Config::getOrDefine('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED_STANDARD);

/* -- Now that we have default directory, we can set the other directories based on it -- */
define('DIR_FILES_UPLOADED_THUMBNAILS', DIR_FILES_UPLOADED . '/thumbnails');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL2', DIR_FILES_UPLOADED . '/thumbnails/level2');
define('DIR_FILES_UPLOADED_THUMBNAILS_LEVEL3', DIR_FILES_UPLOADED . '/thumbnails/level3');
define('DIR_FILES_AVATARS', DIR_FILES_UPLOADED . '/avatars');
define('DIR_FILES_INCOMING', DIR_FILES_UPLOADED . '/incoming');
define('DIR_FILES_AVATARS_STOCK', DIR_FILES_UPLOADED . '/stock_avatars');
define('DIR_FILES_TRASH', DIR_FILES_UPLOADED . '/trash');
define('ENABLE_ALTERNATE_DEFAULT_STORAGE', DIR_FILES_UPLOADED != DIR_FILES_UPLOADED_STANDARD);



/**
 * ----------------------------------------------------------------------------
 * Default cache settings.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('ENABLE_OVERRIDE_CACHE', false);
Config::getOrDefine('ENABLE_BLOCK_CACHE', false);
Config::getOrDefine('ENABLE_ASSET_CACHE', false);
Config::getOrDefine('FULL_PAGE_CACHE_GLOBAL', false);
Config::getOrDefine('FULL_PAGE_CACHE_LIFETIME', 'default');



/**
 * ----------------------------------------------------------------------------
 * Logging settings.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('ENABLE_LOG_EMAILS', true);
Config::getOrDefine('ENABLE_LOG_ERRORS', true);



/**
 * ----------------------------------------------------------------------------
 * Hooks into concrete5.org.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('ENABLE_MARKETPLACE_SUPPORT', true);
Config::getOrDefine('ENABLE_INTELLIGENT_SEARCH_HELP', true);
Config::getOrDefine('ENABLE_APP_NEWS_OVERLAY', true);
Config::getOrDefine('ENABLE_APP_NEWS', true);
if (ENABLE_MARKETPLACE_SUPPORT) {
	Config::getOrDefine('ENABLE_INTELLIGENT_SEARCH_MARKETPLACE', true);
} else {
	Config::getOrDefine('ENABLE_INTELLIGENT_SEARCH_MARKETPLACE', false);
}


/**
 * ----------------------------------------------------------------------------
 * White labeling.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('WHITE_LABEL_LOGO_SRC', false);
defined('WHITE_LABEL_APP_NAME') or define("WHITE_LABEL_APP_NAME", false);



/**
 * ----------------------------------------------------------------------------
 * URL rewriting. Doesn't impact URL_REWRITING_ALL which is set at a lower level
 * and controls whether ALL items will be rewritten (must be a developer)
 * messing around in a file to activate that setting.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('URL_REWRITING', false);



/**
 * ----------------------------------------------------------------------------
 * User information and registration settings.
 * ----------------------------------------------------------------------------
 */
/** -- Registration -- **/
Config::getOrDefine('ENABLE_REGISTRATION_CAPTCHA', true);
Config::getOrDefine('ENABLE_USER_PROFILES', false);
Config::getOrDefine('USER_REGISTRATION_WITH_EMAIL_ADDRESS', false);
Config::getOrDefine('USER_VALIDATE_EMAIL', false);
Config::getOrDefine('USER_REGISTRATION_APPROVAL_REQUIRED', false);
Config::getOrDefine('REGISTER_NOTIFICATION', false);
Config::getOrDefine('EMAIL_ADDRESS_REGISTER_NOTIFICATION', false);
Config::getOrDefine('REGISTRATION_TYPE', 'disabled');
define('ENABLE_REGISTRATION', REGISTRATION_TYPE != 'disabled');

/** -- Profile settings -- **/
Config::getOrDefine('ENABLE_USER_TIMEZONES', false);




/**
 * ----------------------------------------------------------------------------
 * Global permissions and behaviors toggles.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('PERMISSIONS_MODEL', 'simple');
Config::getOrDefine('ENABLE_AREA_LAYOUTS', true);
Config::getOrDefine('ENABLE_CUSTOM_DESIGN', true);
Config::getOrDefine('FORBIDDEN_SHOW_LOGIN', true);
define('PAGE_PERMISSION_IDENTIFIER_USE_PERMISSION_COLLECTION_ID',
	\Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier());


/**
 * ----------------------------------------------------------------------------
 * Date and time constants. Has to come in this file because it must come after
 * localization has been initialized.
 * ----------------------------------------------------------------------------
 */
defined('DATE_APP_GENERIC_MDYT_FULL') or define('DATE_APP_GENERIC_MDYT_FULL', t('F d, Y \a\t g:i A'));
defined('DATE_APP_GENERIC_MDYT_FULL_SECONDS') or define('DATE_APP_GENERIC_MDYT_FULL_SECONDS', t('F d, Y \a\t g:i:s A'));
defined('DATE_APP_GENERIC_MDYT') or define('DATE_APP_GENERIC_MDYT', t('n/j/Y \a\t g:i A'));
defined('DATE_APP_GENERIC_MDY') or define('DATE_APP_GENERIC_MDY', t('n/j/Y'));
defined('DATE_APP_GENERIC_MDY_FULL') or define('DATE_APP_GENERIC_MDY_FULL', t('F j, Y'));
defined('DATE_APP_GENERIC_T') or define('DATE_APP_GENERIC_T', t('g:i A'));
defined('DATE_APP_GENERIC_TS') or define('DATE_APP_GENERIC_TS', t('g:i:s A'));
defined('DATE_APP_FILENAME') or define('DATE_APP_FILENAME', t('d-m-Y_H:i_')); // used when dates are used to start filenames
defined('DATE_APP_FILE_PROPERTIES') or define('DATE_APP_FILE_PROPERTIES', DATE_APP_GENERIC_MDYT_FULL);
defined('DATE_APP_FILE_VERSIONS') or define('DATE_APP_FILE_VERSIONS', DATE_APP_GENERIC_MDYT_FULL);
defined('DATE_APP_FILE_DOWNLOAD') or define('DATE_APP_FILE_DOWNLOAD', DATE_APP_GENERIC_MDYT_FULL);
defined('DATE_APP_PAGE_VERSIONS') or define('DATE_APP_PAGE_VERSIONS', DATE_APP_GENERIC_MDYT);
defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS') or define('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS', DATE_APP_GENERIC_MDYT);
defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES') or define('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES', DATE_APP_GENERIC_MDYT);
defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES') or define('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES', DATE_APP_GENERIC_MDYT);
defined('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY') or define('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY', DATE_APP_GENERIC_MDY);
defined('DATE_APP_DATE_ATTRIBUTE_TYPE_T') or define('DATE_APP_FILE_DOWNLOAD', DATE_APP_GENERIC_TS);
defined('DATE_APP_DATE_PICKER') or define('DATE_APP_DATE_PICKER', t(/*i18n http://api.jqueryui.com/datepicker/#utility-formatDate */'m/d/yy'));
defined('DATE_FORM_HELPER_FORMAT_HOUR') or define('DATE_FORM_HELPER_FORMAT_HOUR', tc(/*i18n: can be 12 or 24 */'Time format', '12'));
if (!defined('DATE_APP_SURVEY_RESULTS')) {
	// NO DEFINE HERE, JUST PLACING HERE TO MAKE A NOTE OF IT
}



/**
 * ----------------------------------------------------------------------------
 * Miscellaneous sitewide settings.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('SITE', 'concrete5');
Config::getOrDefine('STATISTICS_TRACK_PAGE_VIEWS', true);
Config::getOrDefine('ENABLE_PROGRESSIVE_PAGE_REINDEX', true);
Config::getOrDefine('MAIL_SEND_METHOD', 'PHP_MAIL');
Config::getOrDefine('SEO_EXCLUDE_WORDS', 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, since, than, the, this, that, to, up, via, with');
