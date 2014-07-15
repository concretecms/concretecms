<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Install
 */
Route::register('/install', '\Concrete\Controller\Install::view');
Route::register('/install/select_language', '\Concrete\Controller\Install::select_language');
Route::register('/install/setup', '\Concrete\Controller\Install::setup');
Route::register('/install/test_url/{num1}/{num2}', '\Concrete\Controller\Install::test_url');
Route::register('/install/configure', '\Concrete\Controller\Install::configure');
Route::register('/install/run_routine/{pkgHandle}/{routine}', '\Concrete\Controller\Install::run_routine');

/**
 * Tools - legacy
 */
Route::register('/tools/blocks/{btHandle}/{tool}', '\Concrete\Core\Legacy\Controller\ToolController::displayBlock', 'blockTool', array('tool' => '[A-Za-z0-9_/.]+'));
Route::register('/tools/{tool}', '\Concrete\Core\Legacy\Controller\ToolController::display', 'tool', array('tool' => '[A-Za-z0-9_/.]+'));

/**
 * Dialog
 */
Route::register('/ccm/system/dialogs/page/delete/', '\Concrete\Controller\Dialog\Page\Delete::view');
Route::register('/ccm/system/dialogs/page/delete/submit', '\Concrete\Controller\Dialog\Page\Delete::submit');
Route::register('/ccm/system/dialogs/area/layout/presets/submit/{arLayoutID}', '\Concrete\Controller\Dialog\Area\Layout\Presets::submit');
Route::register('/ccm/system/dialogs/area/layout/presets/{arLayoutID}/{token}', '\Concrete\Controller\Dialog\Area\Layout\Presets::view');
Route::register('/ccm/system/dialogs/page/bulk/properties', '\Concrete\Controller\Dialog\Page\Bulk\Properties::view');
Route::register('/ccm/system/dialogs/page/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\Page\Bulk\Properties::updateAttribute');
Route::register('/ccm/system/dialogs/page/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\Page\Bulk\Properties::clearAttribute');
Route::register('/ccm/system/dialogs/page/design', '\Concrete\Controller\Dialog\Page\Design::view');
Route::register('/ccm/system/dialogs/page/design/submit', '\Concrete\Controller\Dialog\Page\Design::submit');
Route::register('/ccm/system/dialogs/user/search', '\Concrete\Controller\Dialog\User\Search::view');
Route::register('/ccm/system/dialogs/group/search', '\Concrete\Controller\Dialog\Group\Search::view');
Route::register('/ccm/system/dialogs/file/search', '\Concrete\Controller\Dialog\File\Search::view');
Route::register('/ccm/system/dialogs/page/design/css', '\Concrete\Controller\Dialog\Page\Design\Css::view');
Route::register('/ccm/system/dialogs/page/design/css/submit', '\Concrete\Controller\Dialog\Page\Design\Css::submit');
Route::register('/ccm/system/dialogs/page/search', '\Concrete\Controller\Dialog\Page\Search::view');
Route::register('/ccm/system/dialogs/page/attributes', '\Concrete\Controller\Dialog\Page\Attributes::view');
Route::register('/ccm/system/dialogs/user/bulk/properties', '\Concrete\Controller\Dialog\User\Bulk\Properties::view');
Route::register('/ccm/system/dialogs/user/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\User\Bulk\Properties::updateAttribute');
Route::register('/ccm/system/dialogs/user/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\User\Bulk\Properties::clearAttribute');
Route::register('/ccm/system/dialogs/file/properties', '\Concrete\Controller\Dialog\File\Properties::view');
Route::register('/ccm/system/dialogs/file/properties/save', '\Concrete\Controller\Dialog\File\Properties::save');
Route::register('/ccm/system/dialogs/file/properties/update_attribute', '\Concrete\Controller\Dialog\File\Properties::update_attribute');
Route::register('/ccm/system/dialogs/file/properties/clear_attribute', '\Concrete\Controller\Dialog\File\Properties::clear_attribute');
Route::register('/ccm/system/dialogs/file/bulk/properties', '\Concrete\Controller\Dialog\File\Bulk\Properties::view');
Route::register('/ccm/system/dialogs/file/bulk/properties/update_attribute', '\Concrete\Controller\Dialog\File\Bulk\Properties::updateAttribute');
Route::register('/ccm/system/dialogs/file/bulk/properties/clear_attribute', '\Concrete\Controller\Dialog\File\Bulk\Properties::clearAttribute');
Route::register('/ccm/system/dialogs/page/add_block_list', '\Concrete\Controller\Dialog\Page\AddBlockList::view');
Route::register('/ccm/system/dialogs/page/clipboard', '\Concrete\Controller\Dialog\Page\Clipboard::view');
Route::register('/ccm/system/dialogs/page/add_block', '\Concrete\Controller\Dialog\Page\AddBlock::view');
Route::register('/ccm/system/dialogs/page/add_block/submit', '\Concrete\Controller\Dialog\Page\AddBlock::submit');
Route::register('/ccm/system/dialogs/page/search/customize', '\Concrete\Controller\Dialog\Page\Search\Customize::view');
Route::register('/ccm/system/dialogs/page/search/customize/submit', '\Concrete\Controller\Dialog\Page\Search\Customize::submit');
Route::register('/ccm/system/dialogs/file/search/customize', '\Concrete\Controller\Dialog\File\Search\Customize::view');
Route::register('/ccm/system/dialogs/file/search/customize/submit', '\Concrete\Controller\Dialog\File\Search\Customize::submit');
Route::register('/ccm/system/dialogs/user/search/customize', '\Concrete\Controller\Dialog\User\Search\Customize::view');
Route::register('/ccm/system/dialogs/user/search/customize/submit', '\Concrete\Controller\Dialog\User\Search\Customize::submit');
Route::register('/ccm/system/dialogs/block/edit/', '\Concrete\Controller\Dialog\Block\Edit::view');
Route::register('/ccm/system/dialogs/block/edit/submit/', '\Concrete\Controller\Dialog\Block\Edit::submit');
Route::register('/ccm/system/dialogs/block/permissions/list/', '\Concrete\Controller\Dialog\Block\Permissions::viewList');
Route::register('/ccm/system/dialogs/block/permissions/detail/', '\Concrete\Controller\Dialog\Block\Permissions::viewDetail');
Route::register('/ccm/system/dialogs/block/permissions/guest_access/', '\Concrete\Controller\Dialog\Block\Permissions\GuestAccess::__construct');
Route::register('/ccm/system/dialogs/block/aliasing/', '\Concrete\Controller\Dialog\Block\Aliasing::view');
Route::register('/ccm/system/dialogs/block/aliasing/submit', '\Concrete\Controller\Dialog\Block\Aliasing::submit');
Route::register('/ccm/system/dialogs/block/design/', '\Concrete\Controller\Dialog\Block\Design::view');
Route::register('/ccm/system/dialogs/block/design/submit', '\Concrete\Controller\Dialog\Block\Design::submit');
Route::register('/ccm/system/dialogs/block/design/reset', '\Concrete\Controller\Dialog\Block\Design::reset');

/**
 * Files
 */
Route::register('/ccm/system/file/star', '\Concrete\Controller\Backend\File::star');
Route::register('/ccm/system/file/rescan', '\Concrete\Controller\Backend\File::rescan');
Route::register('/ccm/system/file/approve_version', '\Concrete\Controller\Backend\File::approveVersion');
Route::register('/ccm/system/file/delete_version', '\Concrete\Controller\Backend\File::deleteVersion');
Route::register('/ccm/system/file/get_json', '\Concrete\Controller\Backend\File::getJSON');
Route::register('/ccm/system/file/duplicate', '\Concrete\Controller\Backend\File::duplicate');
Route::register('/ccm/system/file/upload', '\Concrete\Controller\Backend\File::upload');


/**
 * Users
 */
Route::register('/ccm/system/user/add_group', '\Concrete\Controller\Backend\User::addGroup');
Route::register('/ccm/system/user/remove_group', '\Concrete\Controller\Backend\User::removeGroup');

/**
 * Page actions - non UI
 */
Route::register('/ccm/system/page/check_in/{cID}/{token}', '\Concrete\Controller\Panel\Page\CheckIn::exitEditMode');
Route::register('/ccm/system/page/create/{ptID}', '\Concrete\Controller\Backend\Page::create');
Route::register('/ccm/system/page/arrange_blocks/', '\Concrete\Controller\Backend\Page\ArrangeBlocks::arrange');

/**
 * Block actions - non UI
 */
Route::register('/ccm/system/block/render/', '\Concrete\Controller\Backend\Block::render');

/**
 * Misc
 */
Route::register('/ccm/system/css/page/{cID}/{cvID}/{stylesheet}', '\Concrete\Controller\Frontend\Stylesheet::page');
Route::register('/ccm/system/css/layout/{bID}', '\Concrete\Controller\Frontend\Stylesheet::layout');

/**
 * Search Routes
 */
Route::register('/ccm/system/search/pages/submit', '\Concrete\Controller\Search\Pages::submit');
Route::register('/ccm/system/search/pages/field/{field}', '\Concrete\Controller\Search\Pages::field');
Route::register('/ccm/system/search/files/submit', '\Concrete\Controller\Search\Files::submit');
Route::register('/ccm/system/search/files/field/{field}', '\Concrete\Controller\Search\Files::field');
Route::register('/ccm/system/search/users/submit', '\Concrete\Controller\Search\Users::submit');
Route::register('/ccm/system/search/users/field/{field}', '\Concrete\Controller\Search\Users::field');
Route::register('/ccm/system/search/groups/submit', '\Concrete\Controller\Search\Groups::submit');


/**
 * Panels - top level
 */
Route::register('/ccm/system/panels/dashboard', '\Concrete\Controller\Panel\Dashboard::view');
Route::register('/ccm/system/panels/sitemap', '\Concrete\Controller\Panel\Sitemap::view');
Route::register('/ccm/system/panels/add', '\Concrete\Controller\Panel\Add::view');
Route::register('/ccm/system/panels/page', '\Concrete\Controller\Panel\Page::view');
Route::register('/ccm/system/panels/page/attributes', '\Concrete\Controller\Panel\Page\Attributes::view');
Route::register('/ccm/system/panels/page/design', '\Concrete\Controller\Panel\Page\Design::view');
Route::register('/ccm/system/panels/page/design/preview_contents', '\Concrete\Controller\Panel\Page\Design::preview_contents');
Route::register('/ccm/system/panels/page/design/submit', '\Concrete\Controller\Panel\Page\Design::submit');
Route::register('/ccm/system/panels/page/design/customize/preview/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::preview');
Route::register('/ccm/system/panels/page/design/customize/apply_to_page/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::apply_to_page');
Route::register('/ccm/system/panels/page/design/customize/apply_to_site/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::apply_to_site');
Route::register('/ccm/system/panels/page/design/customize/reset_page_customizations', '\Concrete\Controller\Panel\Page\Design\Customize::reset_page_customizations');
Route::register('/ccm/system/panels/page/design/customize/reset_site_customizations/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::reset_site_customizations');
Route::register('/ccm/system/panels/page/design/customize/{pThemeID}', '\Concrete\Controller\Panel\Page\Design\Customize::view');
Route::register('/ccm/system/panels/page/check_in', '\Concrete\Controller\Panel\Page\CheckIn::__construct');
Route::register('/ccm/system/panels/page/check_in/submit', '\Concrete\Controller\Panel\Page\CheckIn::submit');
Route::register('/ccm/system/panels/page/versions', '\Concrete\Controller\Panel\Page\Versions::view');
Route::register('/ccm/system/panels/page/versions/get_json', '\Concrete\Controller\Panel\Page\Versions::get_json');
Route::register('/ccm/system/panels/page/versions/duplicate', '\Concrete\Controller\Panel\Page\Versions::duplicate');
Route::register('/ccm/system/panels/page/versions/new_page', '\Concrete\Controller\Panel\Page\Versions::new_page');
Route::register('/ccm/system/panels/page/versions/delete', '\Concrete\Controller\Panel\Page\Versions::delete');
Route::register('/ccm/system/panels/page/versions/approve', '\Concrete\Controller\Panel\Page\Versions::approve');
Route::register('/ccm/system/panels/page/preview_as_user', '\Concrete\Controller\Panel\Page\PreviewAsUser::view');
Route::register('/ccm/system/panels/page/preview_as_user/preview', '\Concrete\Controller\Panel\Page\PreviewAsUser::frame_page');
Route::register('/ccm/system/panels/page/preview_as_user/render', '\Concrete\Controller\Panel\Page\PreviewAsUser::preview_page');

/**
 * Panel Details
 */

Route::register('/ccm/system/panels/details/page/versions', '\Concrete\Controller\Panel\Detail\Page\Versions::view');
Route::register('/ccm/system/panels/details/page/seo', '\Concrete\Controller\Panel\Detail\Page\Seo::view');
Route::register('/ccm/system/panels/details/page/seo/submit', '\Concrete\Controller\Panel\Detail\Page\Seo::submit');
Route::register('/ccm/system/panels/details/page/location', '\Concrete\Controller\Panel\Detail\Page\Location::view');
Route::register('/ccm/system/panels/details/page/location/submit', '\Concrete\Controller\Panel\Detail\Page\Location::submit');
Route::register('/ccm/system/panels/details/page/preview', '\Concrete\Controller\Panel\Page\Design::preview');
Route::register('/ccm/system/panels/details/page/composer', '\Concrete\Controller\Panel\Detail\Page\Composer::view');
Route::register('/ccm/system/panels/details/page/composer/autosave', '\Concrete\Controller\Panel\Detail\Page\Composer::autosave');
Route::register('/ccm/system/panels/details/page/composer/publish', '\Concrete\Controller\Panel\Detail\Page\Composer::publish');
Route::register('/ccm/system/panels/details/page/composer/discard', '\Concrete\Controller\Panel\Detail\Page\Composer::discard');
Route::register('/ccm/system/panels/details/page/attributes', '\Concrete\Controller\Panel\Detail\Page\Attributes::view');
Route::register('/ccm/system/panels/details/page/attributes/submit', '\Concrete\Controller\Panel\Detail\Page\Attributes::submit');
Route::register('/ccm/system/panels/details/page/attributes/add_attribute', '\Concrete\Controller\Panel\Detail\Page\Attributes::add_attribute');
Route::register('/ccm/system/panels/details/page/caching', '\Concrete\Controller\Panel\Detail\Page\Caching::view');
Route::register('/ccm/system/panels/details/page/caching/submit', '\Concrete\Controller\Panel\Detail\Page\Caching::submit');
Route::register('/ccm/system/panels/details/page/caching/purge', '\Concrete\Controller\Panel\Detail\Page\Caching::purge');
Route::register('/ccm/system/panels/details/page/permissions', '\Concrete\Controller\Panel\Detail\Page\Permissions::view');
Route::register('/ccm/system/panels/details/page/permissions/save_simple', '\Concrete\Controller\Panel\Detail\Page\Permissions::save_simple');

/**
 * Special Dashboard
 */
Route::register('/dashboard/blocks/stacks/list', function() {
    return Redirect::to('/');
});
