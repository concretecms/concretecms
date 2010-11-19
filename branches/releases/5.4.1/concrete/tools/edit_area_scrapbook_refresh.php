<?php  
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getByID($_REQUEST['cID']);
$cp = new Permissions($c);
if (!$cp->canWrite()) {
	die(_("Access Denied."));
}

$scrapbookName=$_REQUEST['scrapbookName'];
$_SESSION['ccmLastViewedScrapbook']=$scrapbookName;

$a = Area::get($c, $_REQUEST['arHandle']);
$token='&ccm_token='.$_REQUEST['ccm_token']; 

Loader::element('scrapbook_lists', array( 'c'=>$c, 'a'=>$a, 'scrapbookName'=>$scrapbookName, 'token'=>$token ) );  

?>