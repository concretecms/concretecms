<?php

defined('C5_EXECUTE') or define('C5_EXECUTE', md5(uniqid()));

/*
 * ----------------------------------------------------------------------------
 * Assets (Images, JS, etc....) URLs
 * ----------------------------------------------------------------------------
 */
if (APP_UPDATED_PASSTHRU === false) {
    $ap = $app['app_relative_path'] . '/' . DIRNAME_CORE;
} else {
    $ap = $app['app_relative_path'] . '/' . DIRNAME_UPDATES . '/' . DIRNAME_APP_UPDATED . '/' . DIRNAME_CORE;
}

define('ASSETS_URL', $ap);
define('ASSETS_URL_CSS', $ap . '/css');
define('ASSETS_URL_JAVASCRIPT', $ap . '/js');
define('ASSETS_URL_IMAGES', $ap . '/images');

/*
 * ----------------------------------------------------------------------------
 * Relative paths to certain directories and assets. Actually accesses file
 * system
 * ----------------------------------------------------------------------------
 */
define('REL_DIR_APPLICATION', $app['app_relative_path'] . '/' . DIRNAME_APPLICATION);
defined('REL_DIR_STARTING_POINT_PACKAGES') or define('REL_DIR_STARTING_POINT_PACKAGES', REL_DIR_APPLICATION . '/config/install/packages');
define('REL_DIR_STARTING_POINT_PACKAGES_CORE', ASSETS_URL . '/config/install/packages');
define('REL_DIR_PACKAGES', $app['app_relative_path'] . '/packages');
define('REL_DIR_PACKAGES_CORE', ASSETS_URL . '/packages');
define('REL_DIR_FILES_PAGE_TEMPLATE_ICONS', ASSETS_URL_IMAGES . '/icons/page_templates');
define('REL_DIR_FILES_UPLOADED_STANDARD', REL_DIR_APPLICATION . '/files');
define('REL_DIR_AL_ICONS', ASSETS_URL_IMAGES . '/icons/filetypes');
const REL_DIR_FILES_AVATARS = '/avatars';
define('REL_DIR_LANGUAGES_SITE_INTERFACE', REL_DIR_APPLICATION . '/' . DIRNAME_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
define('BLOCK_TYPE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_block_type_generic.png');
define('PACKAGE_GENERIC_ICON', ASSETS_URL_IMAGES . '/icons/icon_package_generic.png');
define('ASSETS_URL_THEMES_NO_THUMBNAIL', ASSETS_URL_IMAGES . '/spacer.gif');
define('AL_ICON_DEFAULT', ASSETS_URL_IMAGES . '/icons/filetypes/default.svg');

/*
 * ----------------------------------------------------------------------------
 * Relative paths to tools. Passes through concrete5.
 * ----------------------------------------------------------------------------
 */
define('REL_DIR_FILES_TOOLS', $app['app_relative_path'] . '/' . DISPATCHER_FILENAME . '/tools');
define('REL_DIR_FILES_TOOLS_REQUIRED', $app['app_relative_path'] . '/' . DISPATCHER_FILENAME . '/tools/required'); // front-end
define('REL_DIR_FILES_TOOLS_BLOCKS', REL_DIR_FILES_TOOLS . '/blocks'); // this maps to the /tools/ directory in the blocks subdir
define('REL_DIR_FILES_TOOLS_PACKAGES', REL_DIR_FILES_TOOLS . '/packages');
