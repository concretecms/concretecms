<?php 

	## This constant ensures that we're operating inside dispatcher.php. There is a LATER check to ensure that dispatcher.php is being called correctly. ##
	define('C5_EXECUTE', true);
	
	## Startup check ##	
	require('startup/config_check.php');

	## Load the base config file ##
	require('config/base.php');

	## First we ensure that dispatcher is not being called directly
	require('startup/file_access_check.php');
	
	## Check host for redirection ##	
	require('startup/url_check.php');
	
	## Load the database ##
	Loader::database();

	## Load required libraries ##
	Loader::library('object');
	Loader::library('log');
	Loader::library('localization');
	Loader::library('request');
	Loader::library('events');
	Loader::library('model');
	Loader::library('item_list');
	Loader::library('view');
	Loader::library('controller');
	Loader::library('block_view');
	Loader::library('block_controller');

	## Autoload settings
	require('startup/autoload.php');
	
	## Load required models ##
	Loader::model('area');
	Loader::model('block');
	Loader::model('block_types');
	Loader::model('collection');
	Loader::model('collection_version');
	Loader::model('config');
	Loader::model('groups');
	Loader::model('package');
	Loader::model('page');
	Loader::model('page_theme');
	Loader::model('permissions');
	Loader::model('user');
	Loader::model('userinfo');

	## Startup cache ##
	Loader::library('cache/abstract');	
	Loader::library('cache/' . CACHE_LIBRARY);	
	Cache::startup();
	
	## Startup check, install ##	
	require('startup/magic_quotes_gpc_check.php');

	## Default routes for various content items ##
	require('config/theme_paths.php');

	## Load session handlers
	require('startup/session.php');

	## Startup check, install ##	
	require('startup/config_check_complete.php');
	
	## User level config ##	
	require('config/app.php');
	
	## Site-level config POST user/app config ##
	if (file_exists(DIR_BASE . '/config/site_post.php')) {
		require(DIR_BASE . '/config/site_post.php');
	}

	## Set debug-related and logging activities
	require('startup/debug_logging.php');

	## Specific site routes for various content items (if they exist) ##
	@include('config/site_theme_paths.php');

	## Specific site/app events if they are enabled ##
	## This must come before packages ##
	if (defined('ENABLE_APPLICATION_EVENTS') && ENABLE_APPLICATION_EVENTS == true) {
		include('config/site_events.php');
	}
	
	## Package events
	require('startup/packages.php');
	
	## Check online, user-related startup routines
	require('startup/user.php');

	// Now we check to see if we're including CSS, Javascript, etc...
	// Include Tools. Format: index.php?task=include_frontend&fType=TOOL&filename=test.php
	require('startup/tools.php');
	
	// figure out where we need to go
	$req = Request::get();
	if ($req->getRequestCollectionPath() != '') {
		$c = Page::getByPath($req->getRequestCollectionPath(), false);		
	} else {
		$c = Page::getByID($req->getRequestCollectionID(), false);
	}

	if ($c->isError()) {
		// if we've gotten an error getting information about this particular collection
		// than we load up the Content class, and get prepared to fire away
		switch($c->getError()) {
			case COLLECTION_NOT_FOUND:
				$v = View::getInstance();
				$v->render('/page_not_found');
				break;
		}
	}

	## Check maintenance mode
	require('startup/maintenance_mode_check.php');
	
	## Check to see whether this is an external alias. If so we go there.
	include('startup/external_link.php');
	
	## Get a permissions object for this particular collection.
	$cp = new Permissions($c);

	## Now that we have a collections and permissions object, we check to make sure
	## everything is okay with collections and permissions

	if ($cp->isError()) {
		// if we've gotten an error getting information about this particular collection
		// than we load up the Content class, and get prepared to fire away
		
		switch($cp->getError()) {
			case COLLECTION_FORBIDDEN:
				$v = View::getInstance();
				$v->render('/page_forbidden');
				break;
		}
	}

	## If there's no error, then we build the collection, but first we load it with the appropriate
	## version. We pass the function the collection object, as well as the collection permissions
	## object, which the function will use to determine what version we get to see

	if ($cp->canWrite() || $cp->canReadVersions()) {
		$cvID = ($_REQUEST['cvID']) ? $_REQUEST['cvID'] : "RECENT";
	} else {
		$cvID = "ACTIVE";
	}

	if ($_REQUEST['ccm-disable-controls'] == true || intval($cvID) > 0) {
		$v = View::getInstance();
		$v->disableEditing();
		$v->disableLinks();
	}
	
	$vp = $c->loadVersionObject($cvID);
	// returns the $vp object, which we then check
	if ($vp->isError()) {
		// if we've gotten an error getting information about this particular collection
		// than we load up the Content class, and get prepared to fire away
		switch($vp->getError()) {
			case VERSION_NOT_RECENT:
				// the collection is not the most recent version. We're not going to allow any writing to the collection
				$cp->disableWrite();
				break;
		}
	}
	
	## Make sure that any submitted forms, etc... are handled correctly
	## This is legacy cms specific stuff, like adding pages
	require('startup/process.php');
	
	## Record the view
	if (STATISTICS_TRACK_PAGE_VIEWS == true) {
		$u->recordView($c);
	}
	
	## now we display (provided we've gotten this far)

	$v = View::getInstance();
	$v->render($c);
?>
