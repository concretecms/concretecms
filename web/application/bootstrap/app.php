<?php

/**
 * ----------------------------------------------------------------------------
 * # Custom Application Handler
 *
 * You can do a lot of things in this file.
 *
 * ## Set a theme by route:
 *
 * Route::setThemeByRoute('/login', 'greek_yogurt');
 *
 *
 * ## Register a class override.
 *
 * Core::bind('helper/feed', function() {
 * 	 return new \Application\Core\CustomFeedHelper();
 * });
 *
 * Core::bind('\Concrete\Attribute\Boolean\Controller', function($app, $params) {
 * 	return new \Application\Attribute\Boolean\Controller($params[0]);
 * });
 *
 * ## Register Events.
 *
 * Events::addListener('on_page_view', function($event) {
 * 	$page = $event->getPageObject();
 * });
 *
 *
 * ## Register some custom MVC Routes
 *
 * Route::register('/test', function() {
 * 	print 'This is a contrived example.';
 * });
 *
 * Route::register('/custom/view', '\My\Custom\Controller::view');
 * Route::register('/custom/add', '\My\Custom\Controller::add');
 *
 * ## Pass some route parameters
 *
 * Route::register('/test/{foo}/{bar}', function($foo, $bar) {
 *  print 'Here is foo: ' . $foo . ' and bar: ' . $bar;
 * });
 *
 *
 * ## Override an Asset
 *
 * use \Concrete\Core\Asset\AssetList;
 * AssetList::getInstance()
 *     ->getAsset('javascript', 'jquery')
 *     ->setAssetURL('/path/to/new/jquery.js');
 *
 * or, override an asset by providing a newer version.
 *
 * use \Concrete\Core\Asset\AssetList;
 * use \Concrete\Core\Asset\Asset;
 * $al = AssetList::getInstance();
 * $al->register(
 *   'javascript', 'jquery', 'path/to/new/jquery.js',
 *   array('version' => '2.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false)
 *   );
 *
 * ----------------------------------------------------------------------------
 */
