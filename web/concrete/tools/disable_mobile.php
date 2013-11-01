<?
defined('C5_EXECUTE') or die("Access Denied.");
/* if the cookie is already set, remove it 
 * really for testing since the cookie has a long lifetime
*/
if(isset($_COOKIE['ccmDisableMobileView']) && $_COOKIE['ccmDisableMobileView'] == true) { 
	setcookie('ccmDisableMobileView', NULL, -1);
	unset($_COOKIE['ccmDisableMobileView']);
} else {
	setcookie('ccmDisableMobileView',true,strtotime('+6 months'),DIR_REL . '/');
}

if($_REQUEST['rcID']) {
	$c = Page::getByID($_REQUEST['rcID'], 'ACTIVE');
	$path = View::url($c->getCollectionPath());
} else {
	$path = View::url('/');
}
header("Location: ".$path);
exit;