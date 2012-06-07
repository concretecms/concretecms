<?php 
defined('C5_EXECUTE') or die("Access Denied.");

// Start the session
if(@ini_get('session.auto_start')) {
	@session_destroy();
}

ini_set('session.use_trans_sid',0);  
session_set_cookie_params(
	(defined('SESSION_COOKIE_PARAM_LIFETIME')?SESSION_COOKIE_PARAM_LIFETIME:0),
	(defined('SESSION_COOKIE_PARAM_PATH')?SESSION_COOKIE_PARAM_PATH:str_replace(' ', '%20', DIR_REL) . '/'),
	(defined('SESSION_COOKIE_PARAM_DOMAIN')?SESSION_COOKIE_PARAM_DOMAIN:''),
	(defined('SESSION_COOKIE_PARAM_SECURE')?SESSION_COOKIE_PARAM_SECURE:false),
	(defined('SESSION_COOKIE_PARAM_HTTPONLY')?SESSION_COOKIE_PARAM_HTTPONLY:false)
	);
	
if (ini_get('session.save_handler') == 'files') {
     ini_set('session.save_path', DIR_SESSIONS);
}

ini_set('session.gc_maxlifetime', SESSION_MAX_LIFETIME);

//if we've set the _postSID variable, we populate session_id using it
if (isset($_POST['ccm-session'])) {
	session_id($_POST['ccm-session']);
} else if (isset($_REQUEST['sessionIDOverride'])) {
	session_id($_REQUEST['sessionIDOverride']);
}

if (isset($_COOKIE[SESSION]) && strlen($_COOKIE[SESSION]) > 32) {
	unset($_COOKIE[SESSION]);
}

session_name(SESSION);
session_start();