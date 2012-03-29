<?php
	## This constant ensures that we're operating inside dispatcher.php. There is a LATER check to ensure that dispatcher.php is being called correctly. ##
	if (!defined("C5_EXECUTE")) {
		define('C5_EXECUTE', true);
	}

	if(defined("E_DEPRECATED")) {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); // E_DEPRECATED required for php 5.3.0 because of depreciated function calls in 3rd party libs (adodb).
	} else {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	}
	
	## Startup check ##	
	require(dirname(__FILE__) . '/config/base_pre.php');

	## Startup check ##	
	require(dirname(__FILE__) . '/startup/config_check.php');
	
	## Check to see if, based on a config variable, we need to point to an alternate core ##
	require(dirname(__FILE__) . '/startup/updated_core_check.php');
	
	## Load the base config file ##
	require(dirname(__FILE__) . '/config/base.php');

	## First we ensure that dispatcher is not being called directly
	require(dirname(__FILE__) . '/startup/file_access_check.php');
	
	## Load the database ##
	Loader::database();

	## Startup cache ##
	Loader::library('cache');	
	Cache::startup();
	
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
	Loader::library('file/types');
	Loader::library('block_view');
	Loader::library('block_view_template');
	Loader::library('block_controller');
	Loader::library('attribute/view');
	Loader::library('attribute/controller');

	set_exception_handler(array('View', 'defaultExceptionHandler'));
	
	## Autoload settings
	if (C5_ENVIRONMENT_ONLY == false) {
		require(dirname(__FILE__) . '/startup/autoload.php');
	}
	
	## Load required models ##
	Loader::model('area');
	Loader::model('global_area');
	Loader::model('attribute/key');
	Loader::model('attribute/value');
	Loader::model('attribute/category');
	Loader::model('attribute/set');
	Loader::model('attribute/type');
	Loader::model('block');
	Loader::model('custom_style');
	Loader::model('file');
	Loader::model('file_version');
	Loader::model('block_types');
	Loader::model('collection');
	Loader::model('collection_version');
	Loader::model('collection_types');
	Loader::model('config');
	Loader::model('groups');
	Loader::model('layout');  
	Loader::model('package');
	Loader::model('page');
	Loader::model('page_theme');
	Loader::model('composer_page');
	Loader::model('permissions');
	Loader::model('user');
	Loader::model('userinfo');
	Loader::model('task_permission');
	Loader::model('stack/model');

	## Set default permissions for new files and directories ##
	require(dirname(__FILE__) . '/startup/file_permission_config.php');
	
	## Setup timzone support
	require(dirname(__FILE__) . '/startup/timezone.php'); // must be included before any date related functions are called (php 5.3 +)

	## Startup check, install ##	
	require(dirname(__FILE__) . '/startup/magic_quotes_gpc_check.php');

	## Default routes for various content items ##
	require(dirname(__FILE__) . '/config/theme_paths.php');

	## Load session handlers
	require(dirname(__FILE__) . '/startup/session.php');

	## Startup check ##	
	require(dirname(__FILE__) . '/startup/encoding_check.php');

	# Startup check, install ##	
	require(dirname(__FILE__) . '/startup/config_check_complete.php');

	## User level config ##	
	require(dirname(__FILE__) . '/config/app.php');

	## Localization ##	
	require(dirname(__FILE__) . '/config/localization.php');

	## File types ##
	## Note: these have to come after config/localization.php ##
	require(dirname(__FILE__) . '/config/file_types.php');
	
	## Check host for redirection ##	
	require(dirname(__FILE__) . '/startup/url_check.php');
	
	## Set debug-related and logging activities
	require(dirname(__FILE__) . '/startup/debug_logging.php');

	## Site-level config POST user/app config ##
	if (file_exists(DIR_BASE . '/config/site_post.php')) {
		require(DIR_BASE . '/config/site_post.php');
	}
	
	## Site-level config POST user/app config - managed by c5, do NOT add your own stuff here ##
	if (file_exists(DIR_BASE . '/config/site_post_restricted.php')) {
		require(DIR_BASE . '/config/site_post_restricted.php');
	}

	require(dirname(__FILE__) . '/startup/tools_upgrade_check.php');

	## Specific site routes for various content items (if they exist) ##
    if (file_exists(DIR_CONFIG_SITE . '/site_theme_paths.php')) {
		@include(DIR_CONFIG_SITE . '/site_theme_paths.php');
    }

	## Specific site routes for various content items (if they exist) ##
	if (file_exists(DIR_CONFIG_SITE . '/site_file_types.php')) {
		@include(DIR_CONFIG_SITE . '/site_file_types.php');
	}

	## Package events
	require(dirname(__FILE__) . '/startup/packages.php');

	## Add additional core menu options
	require(dirname(__FILE__) . '/startup/optional_menu_buttons.php');

	# site events - we have to include before tools
	if (defined('ENABLE_APPLICATION_EVENTS') && ENABLE_APPLICATION_EVENTS == true &&  file_exists(DIR_CONFIG_SITE . '/site_events.php')) {
		@include(DIR_CONFIG_SITE . '/site_events.php');
	}

	// Now we check to see if we're including CSS, Javascript, etc...
	// Include Tools. Format: index.php?task=include_frontend&fType=TOOL&filename=test.php
	require(dirname(__FILE__) . '/startup/tools.php');
	
	## Check online, user-related startup routines
	require(dirname(__FILE__) . '/startup/user.php');

	if (C5_ENVIRONMENT_ONLY == false) {
	
		// figure out where we need to go
		$req = Request::get();
		if ($req->getRequestCollectionPath() != '') {
			if (ENABLE_LEGACY_CONTROLLER_URLS) {
				$c = Page::getByPath($req->getRequestCollectionPath(), false);		
			} else {
				$c = $req->getRequestedPage();
			}
		} else {
			$c = Page::getByID($req->getRequestCollectionID(), false);
		}
	
		$req = Request::get();
		$req->setCurrentPage($c);
		
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
		require(dirname(__FILE__) . '/startup/maintenance_mode_check.php');
		
		## Check to see whether this is an external alias or a header 301 redirect. If so we go there.
		include(dirname(__FILE__) . '/startup/external_link.php');
		
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
					$v->setCollectionObject($c);
					$v->render('/page_forbidden');
					break;
			}
		}

		if (!$c->isActive() && (!$cp->canWrite())) {
			$v = View::getInstance();
			$v->render('/page_not_found');
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
				case COLLECTION_NOT_FOUND:
					$v = View::getInstance();
					$v->render('/page_not_found');
					break;
				case COLLECTION_FORBIDDEN:
					$v = View::getInstance();
					$v->setCollectionObject($c);
					$v->render('/page_forbidden');
					break;
			}
		}
		
		## Any custom site-related process
		if (file_exists(DIR_BASE . '/config/site_process.php')) {
			require(DIR_BASE . '/config/site_process.php');
		}

		## Make sure that any submitted forms, etc... are handled correctly
		## This is legacy cms specific stuff, like adding pages
		require(dirname(__FILE__) . '/startup/process.php');

		## Record the view
		$u = new User();
		if (STATISTICS_TRACK_PAGE_VIEWS == 1) {
			$u->recordView($c);
		}
		
		## Fire the on_page_view Event
		Events::fire('on_page_view', $c, $u);
		
		## now we display (provided we've gotten this far)
	
		$v = View::getInstance();
		$v->render($c);
	}
