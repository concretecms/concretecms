<?

defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

$c = Page::getByID($_REQUEST['cID']);
if (is_object($c) && !$c->isError()) { 
	$cp = new Permissions($c);
	if ($cp->canDeletePage()) { 
		$c->delete();	
		$message = t('Page deleted.');
		
		$obj = new stdClass;
		$obj->message = $message;
		print Loader::helper('json')->encode($obj);
	}
}