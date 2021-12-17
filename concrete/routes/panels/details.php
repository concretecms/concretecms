<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$router->all('/ccm/system/panels/details/page/attributes', '\Concrete\Controller\Panel\Detail\Page\Attributes::view');
$router->all('/ccm/system/panels/details/page/attributes/add_attribute', '\Concrete\Controller\Panel\Detail\Page\Attributes::add_attribute');
$router->all('/ccm/system/panels/details/page/attributes/submit', '\Concrete\Controller\Panel\Detail\Page\Attributes::submit');
$router->all('/ccm/system/panels/details/page/caching', '\Concrete\Controller\Panel\Detail\Page\Caching::view');
$router->all('/ccm/system/panels/details/page/caching/purge', '\Concrete\Controller\Panel\Detail\Page\Caching::purge');
$router->all('/ccm/system/panels/details/page/caching/submit', '\Concrete\Controller\Panel\Detail\Page\Caching::submit');
$router->all('/ccm/system/panels/details/page/composer', '\Concrete\Controller\Panel\Detail\Page\Composer::view');
$router->all('/ccm/system/panels/details/page/composer/autosave', '\Concrete\Controller\Panel\Detail\Page\Composer::autosave');
$router->all('/ccm/system/panels/details/page/composer/discard', '\Concrete\Controller\Panel\Detail\Page\Composer::discard');
$router->all('/ccm/system/panels/details/page/composer/publish', '\Concrete\Controller\Panel\Detail\Page\Composer::publish');
$router->all('/ccm/system/panels/details/page/composer/save_and_exit', '\Concrete\Controller\Panel\Detail\Page\Composer::saveAndExit');
$router->all('/ccm/system/panels/details/page/location', '\Concrete\Controller\Panel\Detail\Page\Location::view');
$router->all('/ccm/system/panels/details/page/location/submit', '\Concrete\Controller\Panel\Detail\Page\Location::submit');
$router->all('/ccm/system/panels/details/page/permissions', '\Concrete\Controller\Panel\Detail\Page\Permissions::view');
$router->all('/ccm/system/panels/details/page/permissions/save_simple', '\Concrete\Controller\Panel\Detail\Page\Permissions::save_simple');
$router->all('/ccm/system/panels/details/page/preview', '\Concrete\Controller\Panel\Page\Design::preview');
$router->all('/ccm/system/panels/details/page/seo', '\Concrete\Controller\Panel\Detail\Page\Seo::view');
$router->all('/ccm/system/panels/details/page/seo/submit', '\Concrete\Controller\Panel\Detail\Page\Seo::submit');
$router->all('/ccm/system/panels/details/page/versions', '\Concrete\Controller\Panel\Detail\Page\Versions::view');
$router->all('/ccm/system/panels/details/page/devices', '\Concrete\Controller\Panel\Page\Devices::detail');
$router->all('/ccm/system/panels/details/theme/preview_preset/{pThemeID}/{presetIdentifier}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewPreset::view');
$router->all('/ccm/system/panels/details/theme/preview_preset_iframe/{pThemeID}/{presetIdentifier}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewPreset::viewIframe');
$router->all('/ccm/system/panels/details/theme/preview_skin/{pThemeID}/{skinIdentifier}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewSkin::view');
$router->all('/ccm/system/panels/details/theme/preview_skin_iframe/{pThemeID}/{skinIdentifier}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewSkin::viewIframe');
$router->all('/ccm/system/panels/details/theme/preview_page_legacy/{pThemeID}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewPageLegacy::view');
$router->all('/ccm/system/panels/details/theme/preview_page_legacy_iframe/{pThemeID}/{pageID}', '\Concrete\Controller\Panel\Detail\Theme\PreviewPageLegacy::viewIframe');
