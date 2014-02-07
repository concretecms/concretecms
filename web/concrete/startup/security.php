<? 
defined('C5_EXECUTE') or die("Access Denied.");
function h($input) {
    return Loader::helper('text')->specialchars($input);
}

function hq($input) {
	return htmlspecialchars($input, ENT_QUOTES, APP_CHARSET, false);
}