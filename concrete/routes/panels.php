<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/panels
 * Namespace: Concrete\Controller\Panel\
 */

$router->all('/add', 'Add::view');
$router->all('/add/get_stack_contents', 'Add::getStackContents');
$router->all('/add/get_stack_folder_contents', 'Add::getStackFolderContents');
$router->all('/add/remove_orphaned_blocks', 'Add::removeOrphanedBlocks');
$router->all('/add/remove_orphaned_block', 'Add::removeOrphanedBlock');
$router->all('/add/get_orphaned_block_contents', 'Add::getOrphanedBlockContents');
$router->all('/add/get_clipboard_contents', 'Add::getClipboardContents');
$router->all('/dashboard', 'Dashboard::view');
$router->all('/dashboard/add_favorite', 'Dashboard::addFavorite');
$router->all('/dashboard/remove_favorite', 'Dashboard::removeFavorite');
$router->all('/page/relations', 'PageRelations::view');
$router->all('/page', 'Page::view');
$router->all('/page/attributes', 'Page\Attributes::view');
$router->all('/page/check_in', 'Page\CheckIn::__construct');
$router->all('/page/check_in/submit', 'Page\CheckIn::submit');
$router->all('/page/design', 'Page\Design::view');
$router->all('/page/design/preview_contents', 'Page\Design::preview_contents');
$router->all('/page/design/submit', 'Page\Design::submit');
$router->all('/page/preview_as_user', 'Page\PreviewAsUser::view');
$router->all('/page/preview_as_user/preview', 'Page\PreviewAsUser::frame_page');
$router->all('/page/preview_as_user/render', 'Page\PreviewAsUser::preview_page');
$router->all('/page/versions', 'Page\Versions::view');
$router->all('/page/versions/get_json', 'Page\Versions::get_json');
$router->all('/page/versions/duplicate', 'Page\Versions::duplicate');
$router->all('/page/versions/new_page', 'Page\Versions::new_page');
$router->all('/page/versions/delete', 'Page\Versions::delete');
$router->all('/page/versions/approve', 'Page\Versions::approve');
$router->all('/page/versions/unapprove', 'Page\Versions::unapprove');
$router->all('/page/versions/revert', 'Page\Versions::revert');
$router->all('/page/devices', 'Page\Devices::view');
$router->all('/page/devices/preview', 'Page\Devices::preview');
$router->all('/theme/customize/theme/{pThemeID}/{previewPageID}', 'Theme\Customize::view');
$router->all('/theme/customize/preset/{pThemeID}/{presetIdentifier}/{previewPageID}', 'Theme\CustomizePreset::view');
$router->all('/theme/customize/skin/{pThemeID}/{skinIdentifier}/{previewPageID}', 'Theme\CustomizePreset::viewSkin');
$router->all('/theme/customize/legacy/{pThemeID}/{previewPageID}', 'Theme\CustomizeLegacy::view');
$router->post('/theme/customize/create_skin/{pThemeID}/{presetIdentifier}', 'Theme\CustomizePreset::createSkin');
$router->post('/theme/customize/save_skin/{pThemeID}/{skinIdentifier}', 'Theme\CustomizePreset::save');
$router->post('/theme/customize/delete_skin/{pThemeID}/{skinIdentifier}', 'Theme\CustomizePreset::delete');
$router->post('/theme/customize/save_styles/{previewPageID}/{pThemeID}/{presetIdentifier}', 'Theme\CustomizePreset::saveStyles');
$router->all('/sitemap', 'Sitemap::view');
$router->all('/help', 'Help::view');
