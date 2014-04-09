<?
defined('C5_EXECUTE') or die("Access Denied.");

$rl = Router::getInstance();

/** 
 * Install
 */
$rl->register('/install', 'Controller\Install::view');
$rl->register('/install/select_language', 'Controller\Install::select_language');
$rl->register('/install/setup', 'Controller\Install::setup');
$rl->register('/install/test_url/{num1}/{num2}', 'Controller\Install::test_url');
$rl->register('/install/configure', 'Controller\Install::configure');
$rl->register('/install/run_routine/{pkgHandle}/{routine}', 'Controller\Install::run_routine');


/** 
 * Tools - legacy
 */
$rl->register('/tools/blocks/{btHandle}/{tool}', 'Core\Legacy\Controller\ToolController::displayBlock', 'blockTool', array('tool' => '[A-Za-z0-9_/]+'));
$rl->register('/tools/{tool}', 'Core\Legacy\Controller\ToolController::display', 'tool', array('tool' => '[A-Za-z0-9_/]+'));

/** 
 * Dialog
 */

$rl->register('/system/dialogs/page/delete/', 'Controller\Dialog\Page\Delete::view');
$rl->register('/system/dialogs/page/delete/submit', 'Controller\Dialog\Page\Delete::submit');
$rl->register('/system/dialogs/area/layout/presets/submit/{arLayoutID}', 'Controller\Dialog\Area\Layout\Presets::submit');
$rl->register('/system/dialogs/area/layout/presets/{arLayoutID}/{token}', 'Controller\Dialog\Area\Layout\Presets::view');
$rl->register('/system/dialogs/page/bulk/properties', 'Controller\Dialog\Page\Bulk\Properties::view');
$rl->register('/system/dialogs/page/bulk/properties/update_attribute', 'Controller\Dialog\Page\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/page/bulk/properties/clear_attribute', 'Controller\Dialog\Page\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/page/design', 'Controller\Dialog\Page\Design::view');
$rl->register('/system/dialogs/page/design/submit', 'Controller\Dialog\Page\Design::submit');
$rl->register('/system/dialogs/user/search', 'Controller\Dialog\User\Search::view');
$rl->register('/system/dialogs/group/search', 'Controller\Dialog\Group\Search::view');
$rl->register('/system/dialogs/file/search', 'Controller\Dialog\File\Search::view');
$rl->register('/system/dialogs/page/search', 'Controller\Dialog\Page\Search::view');
$rl->register('/system/dialogs/page/attributes', 'Controller\Dialog\Page\Attributes::view');
$rl->register('/system/dialogs/user/bulk/properties', 'Controller\Dialog\User\Bulk\Properties::view');
$rl->register('/system/dialogs/user/bulk/properties/update_attribute', 'Controller\Dialog\User\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/user/bulk/properties/clear_attribute', 'Controller\Dialog\User\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/file/properties', 'Controller\Dialog\File\Properties::view');
$rl->register('/system/dialogs/file/properties/save', 'Controller\Dialog\File\Properties::save');
$rl->register('/system/dialogs/file/properties/update_attribute', 'Controller\Dialog\File\Properties::update_attribute');
$rl->register('/system/dialogs/file/properties/clear_attribute', 'Controller\Dialog\File\Properties::clear_attribute');
$rl->register('/system/dialogs/file/bulk/properties', 'Controller\Dialog\File\Bulk\Properties::view');
$rl->register('/system/dialogs/file/bulk/properties/update_attribute', 'Controller\Dialog\File\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/file/bulk/properties/clear_attribute', 'Controller\Dialog\File\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/page/add_block', 'Controller\Dialog\Page\AddBlock::view');
$rl->register('/system/dialogs/page/add_block/submit', 'Controller\Dialog\Page\AddBlock::submit');
$rl->register('/system/dialogs/page/search/customize', 'Controller\Dialog\Page\Search\Customize::view');
$rl->register('/system/dialogs/page/search/customize/submit', 'Controller\Dialog\Page\Search\Customize::submit');
$rl->register('/system/dialogs/file/search/customize', 'Controller\Dialog\File\Search\Customize::view');
$rl->register('/system/dialogs/file/search/customize/submit', 'Controller\Dialog\File\Search\Customize::submit');
$rl->register('/system/dialogs/user/search/customize', 'Controller\Dialog\User\Search\Customize::view');
$rl->register('/system/dialogs/user/search/customize/submit', 'Controller\Dialog\User\Search\Customize::submit');

/**
 * Files
 */
$rl->register('/system/file/star', 'Controller\Backend\File::star');
$rl->register('/system/file/rescan', 'Controller\Backend\File::rescan');
$rl->register('/system/file/approve_version', 'Controller\Backend\File::approveVersion');
$rl->register('/system/file/delete_version', 'Controller\Backend\File::deleteVersion');
$rl->register('/system/file/get_json', 'Controller\Backend\File::getJSON');
$rl->register('/system/file/duplicate', 'Controller\Backend\File::duplicate');
$rl->register('/system/file/upload', 'Controller\Backend\File::upload');


/** 
 * Users
 */
$rl->register('/system/user/add_group', 'Controller\Backend\User::addGroup');
$rl->register('/system/user/remove_group', 'Controller\Backend\User::removeGroup');

/**
 * Page actions - non UI
 */
$rl->register('/system/page/check_in/{cID}/{token}', 'Controller\Panel\Page\CheckIn::exitEditMode');
$rl->register('/system/page/create/{ptID}', 'Controller\Backend\Page::create');
$rl->register('/system/page/arrange_blocks/', 'Controller\Backend\Page\ArrangeBlocks::arrange');


/** 
 * Misc
 */
$rl->register('/system/css/page/{cID}/{cvID}/{stylesheet}', 'Controller\Frontend\Stylesheet::page');
$rl->register('/system/css/layout/{bID}', 'Controller\Frontend\Stylesheet::layout');

/** 
 * Search Routes
 */
$rl->register('/system/search/pages/submit', 'Controller\Search\Pages::submit');
$rl->register('/system/search/pages/field/{field}', 'Controller\Search\Pages::field');
$rl->register('/system/search/files/submit', 'Controller\Search\Files::submit');
$rl->register('/system/search/files/field/{field}', 'Controller\Search\Files::field');
$rl->register('/system/search/users/submit', 'Controller\Search\Users::submit');
$rl->register('/system/search/users/field/{field}', 'Controller\Search\Users::field');
$rl->register('/system/search/groups/submit', 'Controller\Search\Groups::submit');


/** 
 * Panels - top level
 */
$rl->register('/system/panels/dashboard', 'Controller\Panel\Dashboard::view');
$rl->register('/system/panels/sitemap', 'Controller\Panel\Sitemap::view');
$rl->register('/system/panels/add', 'Controller\Panel\Add::view');
$rl->register('/system/panels/page', 'Controller\Panel\Page::view');
$rl->register('/system/panels/page/attributes', 'Controller\Panel\Page\Attributes::view');
$rl->register('/system/panels/page/design', 'Controller\Panel\Page\Design::view');
$rl->register('/system/panels/page/design/preview_contents', 'Controller\Panel\Page\Design::preview_contents');
$rl->register('/system/panels/page/design/submit', 'Controller\Panel\Page\Design::submit');
$rl->register('/system/panels/page/design/customize/preview/{pThemeID}', 'Controller\Panel\Page\Design\Customize::preview');
$rl->register('/system/panels/page/design/customize/apply_to_page/{pThemeID}', 'Controller\Panel\Page\Design\Customize::apply_to_page');
$rl->register('/system/panels/page/design/customize/apply_to_site/{pThemeID}', 'Controller\Panel\Page\Design\Customize::apply_to_site');
$rl->register('/system/panels/page/design/customize/reset_page_customizations', 'Controller\Panel\Page\Design\Customize::reset_page_customizations');
$rl->register('/system/panels/page/design/customize/reset_site_customizations/{pThemeID}', 'Controller\Panel\Page\Design\Customize::reset_site_customizations');
$rl->register('/system/panels/page/design/customize/{pThemeID}', 'Controller\Panel\Page\Design\Customize::view');
$rl->register('/system/panels/page/check_in', 'Controller\Panel\Page\CheckIn::__construct');
$rl->register('/system/panels/page/check_in/submit', 'Controller\Panel\Page\CheckIn::submit');
$rl->register('/system/panels/page/versions', 'Controller\Panel\Page\Versions::view');
$rl->register('/system/panels/page/versions/get_json', 'Controller\Panel\Page\Versions::get_json');
$rl->register('/system/panels/page/versions/duplicate', 'Controller\Panel\Page\Versions::duplicate');
$rl->register('/system/panels/page/versions/new_page', 'Controller\Panel\Page\Versions::new_page');
$rl->register('/system/panels/page/versions/delete', 'Controller\Panel\Page\Versions::delete');
$rl->register('/system/panels/page/versions/approve', 'Controller\Panel\Page\Versions::approve');

/** 
 * Panel Details
 */

$rl->register('/system/panels/details/page/versions', 'Controller\Panel\Detail\Page\Versions::view');
$rl->register('/system/panels/details/page/seo', 'Controller\Panel\Detail\Page\Seo::view');
$rl->register('/system/panels/details/page/seo/submit', 'Controller\Panel\Detail\Page\Seo::submit');
$rl->register('/system/panels/details/page/location', 'Controller\Panel\Detail\Page\Location::view');
$rl->register('/system/panels/details/page/location/submit', 'Controller\Panel\Detail\Page\Location::submit');
$rl->register('/system/panels/details/page/preview', 'Controller\Panel\Detail\Page\Preview::preview');
$rl->register('/system/panels/details/page/composer', 'Controller\Panel\Detail\Page\Composer::view');
$rl->register('/system/panels/details/page/composer/autosave', 'Controller\Panel\Detail\Page\Composer::autosave');
$rl->register('/system/panels/details/page/composer/publish', 'Controller\Panel\Detail\Page\Composer::publish');
$rl->register('/system/panels/details/page/composer/discard', 'Controller\Panel\Detail\Page\Composer::discard');
$rl->register('/system/panels/details/page/attributes', 'Controller\Panel\Detail\Page\Attributes::view');
$rl->register('/system/panels/details/page/attributes/submit', 'Controller\Panel\Detail\Page\Attributes::submit');
$rl->register('/system/panels/details/page/attributes/add_attribute', 'Controller\Panel\Detail\Page\Attributes::add_attribute');
$rl->register('/system/panels/details/page/caching', 'Controller\Panel\Detail\Page\Caching::view');
$rl->register('/system/panels/details/page/caching/submit', 'Controller\Panel\Detail\Page\Caching::submit');
$rl->register('/system/panels/details/page/caching/purge', 'Controller\Panel\Detail\Page\Caching::purge');
$rl->register('/system/panels/details/page/permissions', 'Controller\Panel\Detail\Page\Permissions::view');
$rl->register('/system/panels/details/page/permissions/simple/submit', 'Controller\Panel\Detail\Page\Permissions::save_simple');
$rl->register('/system/panels/details/page/permissions/advanced/submit', 'Controller\Panel\Detail\Page\Permissions::save_advanced');

/** 
 * Page Routes - these must come at the end.
 */
$rl->register('/', 'dispatcher', 'home');
$rl->register('{path}', 'dispatcher', 'page', array('path' => '.+'));

