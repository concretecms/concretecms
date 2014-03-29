<?
defined('C5_EXECUTE') or die("Access Denied.");

$rl = \Concrete\Core\Routing\Router::getInstance();

/** 
 * Install
 */
$rl->register('/install', 'InstallController::view');
$rl->register('/install/select_language', 'InstallController::select_language');
$rl->register('/install/setup', 'InstallController::setup');
$rl->register('/install/test_url/{num1}/{num2}', 'InstallController::test_url');
$rl->register('/install/configure', 'InstallController::configure');
$rl->register('/install/run_routine/{pkgHandle}/{routine}', 'InstallController::run_routine');


/** 
 * Tools - legacy
 */
$rl->register('/tools/blocks/{btHandle}/{tool}', 'ToolController::displayBlock', 'blockTool', array('tool' => '[A-Za-z0-9_/]+'));
$rl->register('/tools/{tool}', 'ToolController::display', 'tool', array('tool' => '[A-Za-z0-9_/]+'));

/** 
 * Editing Interfaces
 */
$rl->register('/system/panels/dashboard', 'DashboardPanelController::view');
$rl->register('/system/panels/sitemap', 'SitemapPanelController::view');
$rl->register('/system/panels/add', 'AddPanelController::view');
$rl->register('/system/panels/page', 'PagePanelController::view');
$rl->register('/system/panels/page/check_in', 'PageCheckInPanelController::__construct');
$rl->register('/system/panels/page/check_in/submit', 'PageCheckInPanelController::submit');
$rl->register('/system/panels/page/design', 'PageDesignPanelController::view');
$rl->register('/system/panels/page/design/preview_contents', 'PageDesignPanelController::preview_contents');
$rl->register('/system/panels/page/design/submit', 'PageDesignPanelController::submit');
$rl->register('/system/panels/page/design/customize/preview/{pThemeID}', 'PageDesignCustomizePanelController::preview');
$rl->register('/system/panels/page/design/customize/apply_to_page/{pThemeID}', 'PageDesignCustomizePanelController::apply_to_page');
$rl->register('/system/panels/page/design/customize/apply_to_site/{pThemeID}', 'PageDesignCustomizePanelController::apply_to_site');
$rl->register('/system/panels/page/design/customize/reset_page_customizations', 'PageDesignCustomizePanelController::reset_page_customizations');
$rl->register('/system/panels/page/design/customize/reset_site_customizations/{pThemeID}', 'PageDesignCustomizePanelController::reset_site_customizations');
$rl->register('/system/panels/page/design/customize/{pThemeID}', 'PageDesignCustomizePanelController::view');
$rl->register('/system/panels/page/versions', 'PageVersionsPanelController::view');
$rl->register('/system/panels/page/versions/get_json', 'PageVersionsPanelController::get_json');
$rl->register('/system/panels/page/versions/duplicate', 'PageVersionsPanelController::duplicate');
$rl->register('/system/panels/page/versions/new_page', 'PageVersionsPanelController::new_page');
$rl->register('/system/panels/page/versions/delete', 'PageVersionsPanelController::delete');
$rl->register('/system/panels/page/versions/approve', 'PageVersionsPanelController::approve');
$rl->register('/system/panels/details/page/versions', 'PageVersionsPanelDetailController::view');
$rl->register('/system/panels/details/page/seo', 'PageSeoPanelDetailController::view');
$rl->register('/system/panels/details/page/seo/submit', 'PageSeoPanelDetailController::submit');
$rl->register('/system/panels/details/page/location', 'PageLocationPanelDetailController::view');
$rl->register('/system/panels/details/page/location/submit', 'PageLocationPanelDetailController::submit');
$rl->register('/system/panels/details/page/preview', 'PageDesignPanelController::preview');
$rl->register('/system/panels/details/page/composer', 'PageComposerPanelDetailController::view');
$rl->register('/system/panels/details/page/composer/autosave', 'PageComposerPanelDetailController::autosave');
$rl->register('/system/panels/details/page/composer/publish', 'PageComposerPanelDetailController::publish');
$rl->register('/system/panels/details/page/composer/discard', 'PageComposerPanelDetailController::discard');
$rl->register('/system/panels/page/attributes', 'PageAttributesPanelController::view');
$rl->register('/system/panels/details/page/attributes', 'PageAttributesPanelDetailController::view');
$rl->register('/system/panels/details/page/attributes/submit', 'PageAttributesPanelDetailController::submit');
$rl->register('/system/panels/details/page/attributes/add_attribute', 'PageAttributesPanelDetailController::add_attribute');
$rl->register('/system/panels/details/page/caching', 'PageCachingPanelDetailController::view');
$rl->register('/system/panels/details/page/caching/submit', 'PageCachingPanelDetailController::submit');
$rl->register('/system/panels/details/page/caching/purge', 'PageCachingPanelDetailController::purge');
$rl->register('/system/panels/details/page/permissions', 'PagePermissionsPanelDetailController::view');
$rl->register('/system/panels/details/page/permissions/simple/submit', 'PagePermissionsPanelDetailController::save_simple');
$rl->register('/system/panels/details/page/permissions/advanced/submit', 'PagePermissionsPanelDetailController::save_advanced');
$rl->register('/system/dialogs/page/delete/', 'PageDeleteDialogController::view');
$rl->register('/system/dialogs/page/delete/submit', 'PageDeleteDialogController::submit');
$rl->register('/system/dialogs/area/layout/presets/submit/{arLayoutID}', 'AreaLayoutPresetsDialogController::submit');
$rl->register('/system/dialogs/area/layout/presets/{arLayoutID}/{token}', 'AreaLayoutPresetsDialogController::view');
$rl->register('/system/dialogs/page/bulk/properties', 'PageBulkPropertiesDialogController::view');
$rl->register('/system/dialogs/page/bulk/properties/update_attribute', 'PageBulkPropertiesDialogController::updateAttribute');
$rl->register('/system/dialogs/page/bulk/properties/clear_attribute', 'PageBulkPropertiesDialogController::clearAttribute');
$rl->register('/system/dialogs/page/design', 'PageDesignDialogController::view');
$rl->register('/system/dialogs/page/design/submit', 'PageDesignDialogController::submit');

$rl->register('/system/dialogs/user/search', 'UserSearchDialogController::view');
$rl->register('/system/dialogs/group/search', 'GroupSearchDialogController::view');
$rl->register('/system/dialogs/file/search', 'FileSearchDialogController::view');
$rl->register('/system/dialogs/page/search', 'PageSearchDialogController::view');

$rl->register('/system/dialogs/page/attributes', 'PageAttributesDialogController::view');

$rl->register('/system/dialogs/user/bulk/properties', 'UserBulkPropertiesDialogController::view');
$rl->register('/system/dialogs/user/bulk/properties/update_attribute', 'UserBulkPropertiesDialogController::updateAttribute');
$rl->register('/system/dialogs/user/bulk/properties/clear_attribute', 'UserBulkPropertiesDialogController::clearAttribute');

$rl->register('/system/dialogs/file/properties', 'FilePropertiesDialogController::view');
$rl->register('/system/dialogs/file/properties/save', 'FilePropertiesDialogController::save');
$rl->register('/system/dialogs/file/properties/update_attribute', 'FilePropertiesDialogController::update_attribute');
$rl->register('/system/dialogs/file/properties/clear_attribute', 'FilePropertiesDialogController::clear_attribute');
$rl->register('/system/dialogs/file/bulk/properties', 'FileBulkPropertiesDialogController::view');
$rl->register('/system/dialogs/file/bulk/properties/update_attribute', 'FileBulkPropertiesDialogController::updateAttribute');
$rl->register('/system/dialogs/file/bulk/properties/clear_attribute', 'FileBulkPropertiesDialogController::clearAttribute');

/**
 * Editing Actions
 */
$rl->register('/system/page/check_in/{cID}/{token}', 'PageCheckInPanelController::exitEditMode');
$rl->register('/system/dialogs/page/add_block', 'PageAddBlockDialogController::view');
$rl->register('/system/dialogs/page/add_block/submit', 'PageAddBlockDialogController::submit');
$rl->register('/system/page/create/{ptID}', 'BackendPageController::create');
$rl->register('/system/page/arrange_blocks/', 'BackendPageArrangeBlocksController::arrange');

/** 
 * Search Routes
 */
$rl->register('/system/dialogs/page/search/customize', 'PageSearchCustomizeDialogController::view');
$rl->register('/system/dialogs/page/search/customize/submit', 'PageSearchCustomizeDialogController::submit');
$rl->register('/system/search/pages/submit', 'SearchPagesController::submit');
$rl->register('/system/search/pages/field/{field}', 'SearchPagesController::field');

$rl->register('/system/dialogs/file/search/customize', 'FileSearchCustomizeDialogController::view');
$rl->register('/system/dialogs/file/search/customize/submit', 'FileSearchCustomizeDialogController::submit');
$rl->register('/system/search/files/submit', 'SearchFilesController::submit');
$rl->register('/system/search/files/field/{field}', 'SearchFilesController::field');

$rl->register('/system/dialogs/user/search/customize', 'UserSearchCustomizeDialogController::view');
$rl->register('/system/dialogs/user/search/customize/submit', 'UserSearchCustomizeDialogController::submit');
$rl->register('/system/search/users/submit', 'SearchUsersController::submit');
$rl->register('/system/search/users/field/{field}', 'SearchUsersController::field');
$rl->register('/system/search/groups/submit', 'SearchGroupsController::submit');

/* Files */
$rl->register('/system/file/star', 'FileController::star');
$rl->register('/system/file/rescan', 'FileController::rescan');
$rl->register('/system/file/approve_version', 'FileController::approveVersion');
$rl->register('/system/file/delete_version', 'FileController::deleteVersion');
$rl->register('/system/file/get_json', 'FileController::getJSON');
$rl->register('/system/file/duplicate', 'FileController::duplicate');
$rl->register('/system/file/upload', 'FileController::upload');

/* Users */
$rl->register('/system/user/add_group', 'UserController::addGroup');
$rl->register('/system/user/remove_group', 'UserController::removeGroup');

/** 
 * Misc
 */
$rl->register('/system/css/page/{cID}/{cvID}/{stylesheet}', 'FrontendStylesheetController::page');
$rl->register('/system/css/layout/{bID}', 'FrontendStylesheetController::layout');

/** 
 * Page Routes - these must come at the end.
 */
$rl->register('/', 'dispatcher', 'home');
$rl->register('{path}', 'dispatcher', 'page', array('path' => '.+'));

