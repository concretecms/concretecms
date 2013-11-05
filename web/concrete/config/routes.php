<?
defined('C5_EXECUTE') or die("Access Denied.");

$rl = Router::getInstance();

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

/**
 * Editing Actions
 */
$rl->register('/system/page/check_in/{cID}/{token}', 'PageCheckInPanelController::exitEditMode');

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

