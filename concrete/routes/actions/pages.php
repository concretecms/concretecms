<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/page
 * Namespace: Concrete\Controller\Backend
 */

$router->all('/arrange_blocks/', 'Page\ArrangeBlocks::arrange');
$router->all('/add_stack/', 'Page\AddStack::addStack');
$router->all('/add_container/', 'Page\AddContainer::addContainer');
$router->all('/checkout/{cID}/{flag}/{token}', 'Page::checkout');
$router->all('/check_in/{cID}/{token}', 'Page::exitEditMode');
$router->all('/create/{ptID}', 'Page::create');
$router->all('/create/{ptID}/{parentID}', 'Page::create');
$router->all('/get_json', 'Page::getJSON');
$router->all('/multilingual/assign', 'Page\Multilingual::assign');
$router->all('/multilingual/create_new', 'Page\Multilingual::create_new');
$router->all('/multilingual/ignore', 'Page\Multilingual::ignore');
$router->all('/multilingual/unmap', 'Page\Multilingual::unmap');
$router->all('/sitemap_data', 'Page\SitemapData::view');
$router->all('/sitemap_delete_forever', 'Page\SitemapDeleteForever::fillQueue');
$router->all('/approve_recent/{cID}/{token}', 'Page::approveRecent');
$router->all('/publish_now/{cID}/{token}', 'Page::publishNow');
$router->all('/cancel_schedule/{cID}/{token}', 'Page::cancelSchedule');
$router->all('/chooser/search/{keywords}', 'Page\Chooser::searchPages');
$router->all('/autocomplete', 'Page\Autocomplete::view');
$router->all('/preview_version', 'Page\PreviewVersion::view');
$router->all('/url_slug', 'Page\UrlSlug::view');

$router->all('/sitemap_overlay', '\Concrete\Controller\Element\Dashboard\Sitemap\SitemapOverlay::view');
