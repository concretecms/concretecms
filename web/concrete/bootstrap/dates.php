<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * ----------------------------------------------------------------------------
 * Assets (Images, JS, etc....) URLs
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