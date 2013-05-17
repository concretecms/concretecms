<?
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
Config::getOrDefine('SITE_LOCALE', 'en_US');

if ($u->getUserDefaultLanguage() != '') {
	define('ACTIVE_LOCALE', $u->getUserDefaultLanguage());
} else if (defined('LOCALE')) {
	define('ACTIVE_LOCALE', LOCALE);
} else {
	define('ACTIVE_LOCALE', SITE_LOCALE);
}

if (strpos(ACTIVE_LOCALE, '_') > -1) {
	$loc = explode('_', ACTIVE_LOCALE);
	if (is_array($loc) && count($loc) == 2) {
		define('LANGUAGE', $loc[0]);
	}
}

define('DIRNAME_LANGUAGES_SITE_INTERFACE', 'site');

if (!defined('DIR_LANGUAGES_SITE_INTERFACE')) {
	define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
}

if (!defined('REL_DIR_LANGUAGES_SITE_INTERFACE')) {
	define('REL_DIR_LANGUAGES_SITE_INTERFACE', DIR_REL . '/' . DIRNAME_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
}

if (!defined("LANGUAGE")) {
	define("LANGUAGE", ACTIVE_LOCALE);
}

if (!defined('ENABLE_TRANSLATE_LOCALE_EN_US')) {
	define('ENABLE_TRANSLATE_LOCALE_EN_US', false);
}

// initialize localization immediately following defining locale
Localization::init();

# New date constants
if (!defined('DATE_APP_GENERIC_MDYT_FULL')) {
	define('DATE_APP_GENERIC_MDYT_FULL', t('F d, Y \a\t g:i A'));
}

if (!defined('DATE_APP_GENERIC_MDYT_FULL_SECONDS')) {
	define('DATE_APP_GENERIC_MDYT_FULL_SECONDS', t('F d, Y \a\t g:i:s A'));
}

if (!defined('DATE_APP_GENERIC_MDYT')) {
	define('DATE_APP_GENERIC_MDYT', t('n/j/Y \a\t g:i A'));
}

if (ACTIVE_LOCALE != 'en_US' && (!defined('DATE_APP_GENERIC_MDY'))) {
	define('DATE_APP_GENERIC_MDY', 'Y-m-d');
	define('DATE_APP_DATE_PICKER', t('yy-mm-dd'));
}

if (!defined('DATE_APP_GENERIC_MDY')) {
	define('DATE_APP_GENERIC_MDY', t('n/j/Y'));
}

if (!defined('DATE_APP_GENERIC_MDY_FULL')) {
	define('DATE_APP_GENERIC_MDY_FULL', t('F j, Y'));
}

if (!defined('DATE_APP_GENERIC_T')) {
	define('DATE_APP_GENERIC_T', t('g:i A'));
}

if (!defined('DATE_APP_GENERIC_TS')) {
	define('DATE_APP_GENERIC_TS', t('g:i:s A'));
}

if (!defined('DATE_APP_FILENAME')) {
	define('DATE_APP_FILENAME', t('d-m-Y_H:i_')); // used when dates are used to start filenames
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
if (!defined('DATE_APP_DATE_PICKER')) {
	define('DATE_APP_DATE_PICKER', 'm/d/yy');
}


if (!defined('DATE_APP_SURVEY_RESULTS')) {
	// NO DEFINE HERE, JUST PLACING HERE TO MAKE A NOTE OF IT
}

if (!defined('DATE_FORM_HELPER_FORMAT_HOUR')) {
	define('DATE_FORM_HELPER_FORMAT_HOUR', tc(/*i18n: can be 12 or 24 */'Time format', '12'));
}
define('BLOCK_NOT_AVAILABLE_TEXT', t('This block is no longer available.'));
define('GUEST_GROUP_NAME', t('Guest'));
define('REGISTERED_GROUP_NAME', t('Registered Users'));
define('ADMIN_GROUP_NAME', t('Admin'));