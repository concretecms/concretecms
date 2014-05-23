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
 * ----------------------------------------------------------------------------
 */

Events::addListener('on_compute_canonical_page_path', function($event) {
    $page = $event->getPageObject();
    $parent = Page::getByID($page->getCollectionParentID());
    if ($parent->getCollectionPath() == '/blog') {
        // strip off the handle
        $path = substr($event->getPagePath(), 0, strrpos($event->getPagePath(), '/'));
        $y = date('Y', strtotime($page->getCollectionDatePublic()));
        $m = date('m', strtotime($page->getCollectionDatePublic()));
        $d = date('d', strtotime($page->getCollectionDatePublic()));
        $path .= sprintf('/%s/%s/%s/', $y, $m, $d);
        $path .= $page->getCollectionHandle();
        $event->setPagePath($path);
        return $event;
    }
});

