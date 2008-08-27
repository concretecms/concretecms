<?

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
	Loader::library('log');
	Loader::library('request');
	Loader::library('object');
	Loader::library('events');
	Loader::library('model');
	Loader::library('view');
	Loader::library('controller');
	Loader::library('block_view');
	Loader::library('block_controller');
	
	## Load required models ##
	Loader::model('area');
	Loader::model('block');
	Loader::model('block_types');
	Loader::model('collection');
	Loader::model('config');
	Loader::model('groups');
	Loader::model('package');
	Loader::model('page');
	Loader::model('page_theme');
	Loader::model('permissions');
	Loader::model('user');
	Loader::model('userinfo');
	Loader::model('version');

	## Startup check, install ##	
	require('startup/config_check_complete.php');
	
	## Load the site's database connection and library
	require('startup/database.php');

	## Set debug-related and logging activities
	require('startup/debug_logging.php');

	## User level config ##	
	require('config/app.php');

	## Default routes for various content items ##
	require('config/theme_paths.php');

	## Specific site routes for various content items (if they exist) ##
	@include('config/site_theme_paths.php');

	## Specific site/app events if they are enabled ##
	if (ENABLE_APPLICATION_EVENTS) {
		include('config/site_events.php');
	}
	
	## Load session handlers
	require('startup/session.php');

	## Check online, user-related startup routines
	require('startup/user.php');

	// Now we check to see if we're including CSS, Javascript, etc...
	// Include Tools. Format: index.php?task=include_frontend&fType=TOOL&filename=test.php
	require('startup/tools.php');
	
	// figure out where we need to go
	$req = ConcreteRequest::get();
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
	$u->recordView($c);

	## now we display (provided we've gotten this far)

	$v = View::getInstance();
	try {
		$v->render($c);
	} catch (Exception $e) {
		$v->renderError('An unexpected error occurred.', $e->getMessage());
	}
?>
