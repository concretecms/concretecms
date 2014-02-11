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
}

if (isset($_COOKIE[SESSION]) && strlen($_COOKIE[SESSION]) > 128) {
	unset($_COOKIE[SESSION]);
}

session_name(SESSION);
session_start();

// generate new id for each new session; do not accept a provided id
if (empty($_SESSION)) {
   session_regenerate_id(true);
}

// avoid session fixation; check IP and UA
if (!empty($_SESSION['client']['REMOTE_ADDR']) && ($_SESSION['client']['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR']) ||
   !empty($_SESSION['client']['HTTP_USER_AGENT']) && ($_SESSION['client']['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])
) {
   // provide new session id and leave the old one
   session_regenerate_id(false);
   // wipe new session
   $_SESSION = array();
}
 
// session defaults
if (empty($_SESSION['client']['REMOTE_ADDR'])) {
   $_SESSION['client']['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
}
if (empty($_SESSION['client']['HTTP_USER_AGENT'])) {
   $_SESSION['client']['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
}