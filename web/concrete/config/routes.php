<?
defined('C5_EXECUTE') or die("Access Denied.");

$rl = Router::getInstance();

/** 
 * Install
 */
$rl->register('/install', '\Concrete\Controller\Install::view');
$rl->register('/install/select_language', '\Concrete\Controller\Install::select_language');
$rl->register('/install/setup', '\Concrete\Controller\Install::setup');
$rl->register('/install/test_url/{num1}/{num2}', '\Concrete\Controller\Install::test_url');
$rl->register('/install/configure', '\Concrete\Controller\Install::configure');
$rl->register('/install/run_routine/{pkgHandle}/{routine}', '\Concrete\Controller\Install::run_routine');


/** 
 * Tools - legacy
 */
$rl->register('/tools/blocks/{btHandle}/{tool}', '\Concrete\Core\Legacy\Controller\ToolController::displayBlock', 'blockTool', array('tool' => '[A-Za-z0-9_/]+'));
$rl->register('/tools/{tool}', '\Concrete\Core\Legacy\Controller\ToolController::display', 'tool', array('tool' => '[A-Za-z0-9_/]+'));

/** 
 * Dialog
 */

$rl->register('/system/dialogs/page/delete/', '\Concrete\Controller\Dialog\Page\Delete::view');
$rl->register('/system/dialogs/page/delete/submit', '\Concrete\Controller\Dialog\Page\Delete::submit');
$rl->register('/system/dialogs/area/layout/presets/submit/{arLayoutID}', '\Concrete\Controller\Dialog\Area\Layout\Presets::submit');
$rl->register('/system/dialogs/area/layout/presets/{arLayoutID}/{token}', '\Concrete\Controller\Dialog\Area\Layout\Presets::view');
$rl->register('/system/dialogs/page/bulk/properties', '\Concrete\Controller\Dialog\Page\Bulk\Properties::view');
$rl->register('/system/dialogs/page/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\Page\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/page/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\Page\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/page/design', '\Concrete\Controller\Dialog\Page\Design::view');
$rl->register('/system/dialogs/page/design/submit', '\Concrete\Controller\Dialog\Page\Design::submit');
$rl->register('/system/dialogs/user/search', '\Concrete\Controller\Dialog\User\Search::view');
$rl->register('/system/dialogs/group/search', '\Concrete\Controller\Dialog\Group\Search::view');
$rl->register('/system/dialogs/file/search', '\Concrete\Controller\Dialog\File\Search::view');
$rl->register('/system/dialogs/page/search', '\Concrete\Controller\Dialog\Page\Search::view');
$rl->register('/system/dialogs/page/attributes', '\Concrete\Controller\Dialog\Page\Attributes::view');
$rl->register('/system/dialogs/user/bulk/properties', '\Concrete\Controller\Dialog\User\Bulk\Properties::view');
$rl->register('/system/dialogs/user/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\User\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/user/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\User\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/file/properties', '\Concrete\Controller\Dialog\File\Properties::view');
$rl->register('/system/dialogs/file/properties/save', '\Concrete\Controller\Dialog\File\Properties::save');
$rl->register('/system/dialogs/file/properties/update_attribute', '\Concrete\Controller\Dialog\File\Properties::update_attribute');
$rl->register('/system/dialogs/file/properties/clear_attribute', '\Concrete\Controller\Dialog\File\Properties::clear_attribute');
$rl->register('/system/dialogs/file/bulk/properties', '\Concrete\Controller\Dialog\File\Bulk\Properties::view');
$rl->register('/system/dialogs/file/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\File\Bulk\Properties::updateAttribute');
$rl->register('/system/dialogs/file/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\File\Bulk\Properties::clearAttribute');
$rl->register('/system/dialogs/page/add_block', '\Concrete\Controller\Dialog\Page\AddBlock::view');
$rl->register('/system/dialogs/page/add_block/submit', '\Concrete\Controller\Dialog\Page\AddBlock::submit');
$rl->register('/system/dialogs/page/search/customize', '\Concrete\Controller\Dialog\Page\Search\Customize::view');
$rl->register('/system/dialogs/page/search/customize/submit', '\Concrete\Controller\Dialog\Page\Search\Customize::submit');
$rl->register('/system/dialogs/file/search/customize', '\Concrete\Controller\Dialog\File\Search\Customize::view');
$rl->register('/system/dialogs/file/search/customize/submit', '\Concrete\Controller\Dialog\File\Search\Customize::submit');
$rl->register('/system/dialogs/user/search/customize', '\Concrete\Controller\Dialog\User\Search\Customize::view');
$rl->register('/system/dialogs/user/search/customize/submit', '\Concrete\Controller\Dialog\User\Search\Customize::submit');

/**
 * Files
 */
$rl->register('/system/file/star', '\Concrete\Controller\Backend\File::star');
$rl->register('/system/file/rescan', '\Concrete\Controller\Backend\File::rescan');
$rl->register('/system/file/approve_version', '\Concrete\Controller\Backend\File::approveVersion');
$rl->register('/system/file/delete_version', '\Concrete\Controller\Backend\File::deleteVersion');
$rl->register('/system/file/get_json', '\Concrete\Controller\Backend\File::getJSON');
$rl->register('/system/file/duplicate', '\Concrete\Controller\Backend\File::duplicate');
$rl->register('/system/file/upload', '\Concrete\Controller\Backend\File::upload');


/** 
 * Users
 */
$rl->register('/system/user/add_group', '\Concrete\Controller\Backend\User::addGroup');
$rl->register('/system/user/remove_group', '\Concrete\Controller\Backend\User::removeGroup');

/**
 * Page actions - non UI
 */
$rl->register('/system/page/check_in/{cID}/{token}', '\Concrete\Controller\Panel\Page\CheckIn::exitEditMode');
$rl->register('/system/page/create/{ptID}', '\Concrete\Controller\Backend\Page::create');
$rl->register('/system/page/arrange_blocks/', '\Concrete\Controller\Backend\Page\ArrangeBlocks::arrange');


/** 
 * Misc
 */
$rl->register('/system/css/page/{cID}/{cvID}/{stylesheet}', '\Concrete\Controller\Frontend\Stylesheet::page');
$rl->register('/system/css/layout/{bID}', '\Concrete\Controller\Frontend\Stylesheet::layout');

/** 
 * Search Routes
 */
$rl->register('/system/search/pages/submit', '\Concrete\Controller\Search\Pages::submit');
$rl->register('/system/search/pages/field/{field}', '\Concrete\Controller\Search\Pages::field');
$rl->register('/system/search/files/submit', '\Concrete\Controller\Search\Files::submit');
$rl->register('/system/search/files/field/{field}', '\Concrete\Controller\Search\Files::field');
$rl->register('/system/search/users/submit', '\Concrete\Controller\Search\Users::submit');
$rl->register('/system/search/users/field/{field}', '\Concrete\Controller\Search\Users::field');
$rl->register('/system/search/groups/submit', '\Concrete\Controller\Search\Groups::submit');


/** 
 * Panels - top level
 */
$rl->register('/system/panels/dashboard', '\Concrete\Controller\Panel\Dashboard::view');
$rl->register('/system/panels/sitemap', '\Concrete\Controller\Panel\Sitemap::view');
$rl->register('/system/panels/add', '\Concrete\Controller\Panel\Add::view');
$rl->register('/system/panels/page', '\Concrete\Controller\Panel\Page::view');
$rl->register('/system/panels/page/attributes', '\Concrete\Controller\Panel\Page\Attributes::view');
$rl->register('/system/panels/page/design', '\Concrete\Controller\Panel\Page\Design::view');
$rl->register('/system/panels/page/design/preview_contents', '\Concrete\Controller\Panel\Page\Design::preview_contents');
$rl->register('/system/panels/page/design/submit', '\Concrete\Controller\Panel\Page\Design::submit');
$rl->register('/system/panels/page/design/customize/preview/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::preview');
$rl->register('/system/panels/page/design/customize/apply_to_page/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::apply_to_page');
$rl->register('/system/panels/page/design/customize/apply_to_site/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::apply_to_site');
$rl->register('/system/panels/page/design/customize/reset_page_customizations', '\Concrete\Controller\Panel\Page\Design\Customize::reset_page_customizations');
$rl->register('/system/panels/page/design/customize/reset_site_customizations/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::reset_site_customizations');
$rl->register('/system/panels/page/design/customize/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::view');
$rl->register('/system/panels/page/check_in', '\Concrete\Controller\Panel\Page\CheckIn::__construct');
$rl->register('/system/panels/page/check_in/submit', '\Concrete\Controller\Panel\Page\CheckIn::submit');
$rl->register('/system/panels/page/versions', '\Concrete\Controller\Panel\Page\Versions::view');
$rl->register('/system/panels/page/versions/get_json', '\Concrete\Controller\Panel\Page\Versions::get_json');
$rl->register('/system/panels/page/versions/duplicate', '\Concrete\Controller\Panel\Page\Versions::duplicate');
$rl->register('/system/panels/page/versions/new_page', '\Concrete\Controller\Panel\Page\Versions::new_page');
$rl->register('/system/panels/page/versions/delete', '\Concrete\Controller\Panel\Page\Versions::delete');
$rl->register('/system/panels/page/versions/approve', '\Concrete\Controller\Panel\Page\Versions::approve');

/** 
 * Panel Details
 */

$rl->register('/system/panels/details/page/versions', '\Concrete\Controller\Panel\Detail\Page\Versions::view');
$rl->register('/system/panels/details/page/seo', '\Concrete\Controller\Panel\Detail\Page\Seo::view');
$rl->register('/system/panels/details/page/seo/submit', '\Concrete\Controller\Panel\Detail\Page\Seo::submit');
$rl->register('/system/panels/details/page/location', '\Concrete\Controller\Panel\Detail\Page\Location::view');
$rl->register('/system/panels/details/page/location/submit', '\Concrete\Controller\Panel\Detail\Page\Location::submit');
$rl->register('/system/panels/details/page/preview', '\Concrete\Controller\Panel\Page\Design::preview');
$rl->register('/system/panels/details/page/composer', '\Concrete\Controller\Panel\Detail\Page\Composer::view');
$rl->register('/system/panels/details/page/composer/autosave', '\Concrete\Controller\Panel\Detail\Page\Composer::autosave');
$rl->register('/system/panels/details/page/composer/publish', '\Concrete\Controller\Panel\Detail\Page\Composer::publish');
$rl->register('/system/panels/details/page/composer/discard', '\Concrete\Controller\Panel\Detail\Page\Composer::discard');
$rl->register('/system/panels/details/page/attributes', '\Concrete\Controller\Panel\Detail\Page\Attributes::view');
$rl->register('/system/panels/details/page/attributes/submit', '\Concrete\Controller\Panel\Detail\Page\Attributes::submit');
$rl->register('/system/panels/details/page/attributes/add_attribute', '\Concrete\Controller\Panel\Detail\Page\Attributes::add_attribute');
$rl->register('/system/panels/details/page/caching', '\Concrete\Controller\Panel\Detail\Page\Caching::view');
$rl->register('/system/panels/details/page/caching/submit', '\Concrete\Controller\Panel\Detail\Page\Caching::submit');
$rl->register('/system/panels/details/page/caching/purge', '\Concrete\Controller\Panel\Detail\Page\Caching::purge');
$rl->register('/system/panels/details/page/permissions', '\Concrete\Controller\Panel\Detail\Page\Permissions::view');
$rl->register('/system/panels/details/page/permissions/simple/submit', '\Concrete\Controller\Panel\Detail\Page\Permissions::save_simple');
$rl->register('/system/panels/details/page/permissions/advanced/submit', '\Concrete\Controller\Panel\Detail\Page\Permissions::save_advanced');

/** 
 * Special Dashboard
 */
$rl->register('/dashboard/blocks/stacks/list', function() {
	return Redirect::to('/');
});

/** 
 * Page Routes - these must come at the end.
 */
$rl->register('/', 'dispatcher', 'home');
$rl->register('{path}', 'dispatcher', 'page', array('path' => '.+'));

