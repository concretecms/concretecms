<?php
/**
 * ----------------------------------------------------------------------------
 * # Custom Application Handler
 *
 * You can do a lot of things in this file.
 *
 * ## Set a theme by route:
 *
 * $router = Router::getInstance();
 * $router->setThemeByRoute('/login', 'greek_yogurt');
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
 * $router = Router::getInstance();
 * $router->register('/test', function() {
 * 	print 'This is a contrived example.';
 * });
 *
 * $router->register('/custom/view', '\My\Custom\Controller::view');
 * $router->register('/custom/add', '\My\Custom\Controller::add');
 *
 * ----------------------------------------------------------------------------
 */

